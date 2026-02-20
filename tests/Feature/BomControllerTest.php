<?php

namespace Tests\Feature;

use App\Models\Bom;
use App\Models\BomRevision;
use App\Models\BomRevisionElement;
use App\Models\Location;
use App\Models\Part;
use App\Models\StockLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BomControllerTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    /**
     * Creates a BOM in the NEW model:
     * - creates BOM container
     * - creates Rev 1
     * - sets boms.current_revision_id = rev1
     */
    private function createBomFor(User $user, array $attrs = []): Bom
    {
        $bom = new Bom([
            'bom_name' => $attrs['bom_name'] ?? 'Test BOM',
            'bom_description' => $attrs['bom_description'] ?? 'Test Desc',
            'bom_owner_u_fk' => $user->id,
        ]);
        $bom->save();

        $rev = BomRevision::create([
            'bom_id_fk' => $bom->bom_id,
            'revision_number' => 1,
            'label' => $attrs['revision_label'] ?? null,
            'note' => $attrs['revision_note'] ?? null,
            'created_by_u_fk' => $user->id,
        ]);

        $bom->current_revision_id = $rev->bom_revision_id;
        $bom->save();

        return $bom;
    }

    /**
     * Attaches a BOM line in the NEW model:
     * - writes to bom_revision_elements of the BOM's current revision
     */
    private function attachBomElement(Bom $bom, Part $part, int $qty): void
    {
        $revId = (int) ($bom->current_revision_id ?? 0);
        if ($revId <= 0) {
            $this->fail('Test BOM has no current_revision_id. Did createBomFor() run?');
        }

        BomRevisionElement::create([
            'bom_revision_id_fk' => $revId,
            'part_id_fk' => $part->part_id,
            'quantity' => $qty,
            'refdes' => null,
            'line_uuid' => null,
        ]);
    }

    public function test_user_cannot_view_someone_elses_bom(): void
    {
        $owner = $this->verifiedUser();
        $intruder = $this->verifiedUser();

        $bom = $this->createBomFor($owner);

        $this->actingAs($intruder)
            ->get("/bom/{$bom->bom_id}")
            ->assertStatus(403);
    }

    public function test_bom_import_creates_bom_and_elements(): void
    {
        Storage::fake('local');

        $user = $this->verifiedUser();
        $this->actingAs($user);

        $p1 = Part::factory()->create(['part_owner_u_fk' => $user->id]);
        $p2 = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        $csv = implode("\n", [
            'part_id,part_name,quantity',
            "{$p1->part_id},,7",
            ",{$p2->part_name},3",
        ]);

        $file = UploadedFile::fake()->createWithContent('bom.csv', $csv);

        $resp = $this->postJson('/bom.import', [
            'bom_name' => 'Imported BOM',
            'bom_description' => 'From test',
            'bom_file' => $file,
        ]);

        $resp->assertStatus(200)
            ->assertJsonStructure(['success', 'new_bom_id']);

        $bomId = (int) $resp->json('new_bom_id');

        $this->assertDatabaseHas('boms', [
            'bom_id' => $bomId,
            'bom_owner_u_fk' => $user->id,
        ]);

        $rev = BomRevision::where('bom_id_fk', $bomId)
            ->where('revision_number', 1)
            ->firstOrFail();

        $this->assertDatabaseHas('boms', [
            'bom_id' => $bomId,
            'current_revision_id' => $rev->bom_revision_id,
        ]);

        $this->assertDatabaseHas('bom_revision_elements', [
            'bom_revision_id_fk' => $rev->bom_revision_id,
            'part_id_fk' => $p1->part_id,
            'quantity' => 7,
        ]);

        $this->assertDatabaseHas('bom_revision_elements', [
            'bom_revision_id_fk' => $rev->bom_revision_id,
            'part_id_fk' => $p2->part_id,
            'quantity' => 3,
        ]);
    }

    public function test_assembling_bom_reduces_stock_and_creates_bom_run(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $location = Location::factory()->create([
            'location_owner_u_fk' => $user->id,
        ]);

        $bom = $this->createBomFor($user);

        $partA = Part::factory()->create(['part_owner_u_fk' => $user->id]);
        $partB = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        // BOM requires: A x2, B x5
        $this->attachBomElement($bom, $partA, 2);
        $this->attachBomElement($bom, $partB, 5);

        StockLevel::createStockLevelRecord($partA->part_id, $location->location_id, 20);
        StockLevel::createStockLevelRecord($partB->part_id, $location->location_id, 50);

        // Assemble quantity 3 => reduce A by 6, B by 15
        $resp = $this->postJson('/bom.assemble', [
            'ids' => [$bom->bom_id],
            'assemble_quantity' => 3,
            'from_location' => $location->location_id,
        ]);

        $resp->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('stock_levels', [
            'part_id_fk' => $partA->part_id,
            'location_id_fk' => $location->location_id,
            'stock_level_quantity' => 14,
        ]);

        $this->assertDatabaseHas('stock_levels', [
            'part_id_fk' => $partB->part_id,
            'location_id_fk' => $location->location_id,
            'stock_level_quantity' => 35,
        ]);

        $this->assertDatabaseHas('bom_runs', [
            'bom_id_fk' => $bom->bom_id,
            'bom_run_quantity' => 3,
            'bom_run_user_fk' => $user->id,
        ]);
    }

    public function test_assembling_bom_with_insufficient_stock_requests_permission_and_does_not_write(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $location = Location::factory()->create([
            'location_owner_u_fk' => $user->id,
        ]);

        $bom = $this->createBomFor($user);

        $part = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        // Needs 10 per build
        $this->attachBomElement($bom, $part, 10);

        // Only 5 in stock -> assembling 1 will go negative
        StockLevel::createStockLevelRecord($part->part_id, $location->location_id, 5);

        $resp = $this->postJson('/bom.assemble', [
            'ids' => [$bom->bom_id],
            'assemble_quantity' => 1,
            'from_location' => $location->location_id,
        ]);

        $resp->assertStatus(200)
            ->assertJson([
                'status' => 'permission_requested',
            ])
            ->assertJsonStructure([
                'changes',
                'negative_stock',
                'negative_stock_table',
            ]);

        // Stock must remain unchanged (no writes yet)
        $this->assertDatabaseHas('stock_levels', [
            'part_id_fk' => $part->part_id,
            'location_id_fk' => $location->location_id,
            'stock_level_quantity' => 5,
        ]);

        // No bom_run should be created when permission is requested
        $this->assertDatabaseMissing('bom_runs', [
            'bom_id_fk' => $bom->bom_id,
            'bom_run_user_fk' => $user->id,
        ]);
    }

    public function test_user_cannot_assemble_someone_elses_bom(): void
    {
        $owner = $this->verifiedUser();
        $intruder = $this->verifiedUser();

        $location = Location::factory()->create([
            'location_owner_u_fk' => $owner->id,
        ]);

        $bom = $this->createBomFor($owner);

        $part = Part::factory()->create(['part_owner_u_fk' => $owner->id]);
        $this->attachBomElement($bom, $part, 1);

        StockLevel::createStockLevelRecord($part->part_id, $location->location_id, 10);

        $resp = $this->actingAs($intruder)->postJson('/bom.assemble', [
            'ids' => [$bom->bom_id],
            'assemble_quantity' => 1,
            'from_location' => $location->location_id,
        ]);

        $resp->assertStatus(403);
    }

    public function test_bom_create_requires_at_least_one_line(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $resp = $this->postJson('/bom.create', [
            'bom_name' => 'Empty BOM',
            'bom_description' => 'Should fail',
            'revision_label' => null,
            'revision_note' => null,
            'lines' => [],
            'type' => 'bom',
        ]);

        $resp->assertStatus(422);
    }

    public function test_bom_create_creates_rev1_sets_current_revision_and_elements(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $p1 = Part::factory()->create(['part_owner_u_fk' => $user->id]);
        $p2 = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        $resp = $this->postJson('/bom.create', [
            'bom_name' => 'New BOM',
            'bom_description' => 'Created from test',
            'revision_label' => 'Initial',
            'revision_note' => 'Hello',
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 2, 'refdes' => 'R1,R2', 'line_uuid' => 'u1'],
                ['part_id' => $p2->part_id, 'quantity' => 5, 'refdes' => null, 'line_uuid' => 'u2'],
            ],
            'type' => 'bom',
        ]);

        $resp->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure(['new_bom_id']);

        $bomId = (int) $resp->json('new_bom_id');

        $bom = Bom::findOrFail($bomId);
        $this->assertNotNull($bom->current_revision_id);

        $this->assertDatabaseHas('bom_revisions', [
            'bom_id_fk' => $bomId,
            'revision_number' => 1,
            'bom_revision_id' => $bom->current_revision_id,
            'created_by_u_fk' => $user->id,
        ]);

        $this->assertDatabaseHas('bom_revision_elements', [
            'bom_revision_id_fk' => $bom->current_revision_id,
            'part_id_fk' => $p1->part_id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('bom_revision_elements', [
            'bom_revision_id_fk' => $bom->current_revision_id,
            'part_id_fk' => $p2->part_id,
            'quantity' => 5,
        ]);
    }

    public function test_bom_commit_rejects_if_base_revision_id_is_not_current(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $p1 = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        // Create via endpoint so schema matches reality
        $create = $this->postJson('/bom.create', [
            'bom_name' => 'Commit Test BOM',
            'bom_description' => null,
            'revision_label' => null,
            'revision_note' => null,
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 1, 'refdes' => null, 'line_uuid' => 'u1'],
            ],
            'type' => 'bom',
        ])->assertStatus(200);

        $bomId = (int) $create->json('new_bom_id');
        $bom = Bom::findOrFail($bomId);
        $current = (int) $bom->current_revision_id;

        $wrongBase = $current + 999;

        $resp = $this->postJson("/bom/{$bomId}/commit", [
            'base_revision_id' => $wrongBase,
            'revision_label' => 'Rev2',
            'revision_note' => null,
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 2, 'refdes' => null, 'line_uuid' => 'u2'],
            ],
            'type' => 'bom',
        ]);

        $resp->assertStatus(409);
    }

    public function test_bom_commit_creates_rev2_and_updates_current_revision_id(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $p1 = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        $create = $this->postJson('/bom.create', [
            'bom_name' => 'Rev2 BOM',
            'bom_description' => null,
            'revision_label' => null,
            'revision_note' => null,
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 1, 'refdes' => null, 'line_uuid' => 'u1'],
            ],
            'type' => 'bom',
        ])->assertStatus(200);

        $bomId = (int) $create->json('new_bom_id');
        $bom = Bom::findOrFail($bomId);
        $rev1Id = (int) $bom->current_revision_id;

        $resp = $this->postJson("/bom/{$bomId}/commit", [
            'base_revision_id' => $rev1Id,
            'revision_label' => 'Second',
            'revision_note' => 'note',
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 3, 'refdes' => 'R1,R2,R3', 'line_uuid' => 'u2'],
            ],
            'type' => 'bom',
        ]);

        $resp->assertStatus(200)->assertJson(['status' => 'success']);

        $bom->refresh();
        $rev2Id = (int) $bom->current_revision_id;

        $this->assertNotEquals($rev1Id, $rev2Id);

        $this->assertDatabaseHas('bom_revisions', [
            'bom_id_fk' => $bomId,
            'revision_number' => 2,
            'bom_revision_id' => $rev2Id,
        ]);

        $this->assertDatabaseHas('bom_revision_elements', [
            'bom_revision_id_fk' => $rev2Id,
            'part_id_fk' => $p1->part_id,
            'quantity' => 3,
        ]);

        // Rev1 snapshot must remain unchanged
        $this->assertDatabaseHas('bom_revision_elements', [
            'bom_revision_id_fk' => $rev1Id,
            'part_id_fk' => $p1->part_id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('bom_revisions', [
            'bom_revision_id' => $rev2Id,
            'label' => 'Second',
            'note' => 'note',
        ]);

    }

    public function test_free_user_requesting_old_revision_is_forced_to_current_revision(): void
    {
        $user = $this->verifiedUser(); // no subscription => free
        $this->actingAs($user);

        $p1 = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        $create = $this->postJson('/bom.create', [
            'bom_name' => 'Gate BOM',
            'bom_description' => null,
            'revision_label' => null,
            'revision_note' => null,
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 1, 'refdes' => null, 'line_uuid' => 'u1'],
            ],
            'type' => 'bom',
        ])->assertStatus(200);

        $bomId = (int) $create->json('new_bom_id');
        $bom = Bom::findOrFail($bomId);
        $rev1Id = (int) $bom->current_revision_id;

        // create Rev2
        $this->postJson("/bom/{$bomId}/commit", [
            'base_revision_id' => $rev1Id,
            'revision_label' => 'Rev2',
            'revision_note' => null,
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 2, 'refdes' => null, 'line_uuid' => 'u2'],
            ],
            'type' => 'bom',
        ])->assertStatus(200);

        $bom->refresh();
        $rev2Id = (int) $bom->current_revision_id;

        // free user tries to view rev1 via query
        $resp = $this->get("/bom/{$bomId}?rev={$rev1Id}");

        $resp->assertStatus(200);

        // Controller should force to current (rev2)
        $resp->assertViewHas('selected_revision_id', $rev2Id);
        $resp->assertViewHas('revision_history_locked', true);
    }

    public function test_commit_requires_at_least_one_line(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $p1 = Part::factory()->create(['part_owner_u_fk' => $user->id]);

        $create = $this->postJson('/bom.create', [
            'bom_name' => 'Empty Commit Test',
            'lines' => [
                ['part_id' => $p1->part_id, 'quantity' => 1],
            ],
            'type' => 'bom',
        ])->assertStatus(200);

        $bomId = $create->json('new_bom_id');
        $bom = Bom::findOrFail($bomId);

        $resp = $this->postJson("/bom/{$bomId}/commit", [
            'base_revision_id' => $bom->current_revision_id,
            'lines' => [],   // ← this should fail
            'type' => 'bom',
        ]);

        $resp->assertStatus(422);
    }
}
