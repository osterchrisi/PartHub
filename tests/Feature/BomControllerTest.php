<?php

namespace Tests\Feature;

use App\Models\Bom;
use App\Models\BomElements;
use App\Models\BomRun;
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

    private function createBomFor(User $user, array $attrs = []): Bom
    {
        $bom = new Bom([
            'bom_name' => $attrs['bom_name'] ?? 'Test BOM',
            'bom_description' => $attrs['bom_description'] ?? 'Test Desc',
        ]);

        $bom->bom_owner_u_fk = $user->id;
        $bom->save();

        return $bom;
    }

    private function attachBomElement(Bom $bom, Part $part, int $qty): void
    {
        BomElements::create([
            'bom_id_fk' => $bom->bom_id,
            'part_id_fk' => $part->part_id,
            'element_quantity' => $qty,
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
            "part_id,part_name,quantity",
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

        $bomId = $resp->json('new_bom_id');

        $this->assertDatabaseHas('boms', [
            'bom_id' => $bomId,
            'bom_owner_u_fk' => $user->id,
        ]);

        $this->assertDatabaseHas('bom_elements', [
            'bom_id_fk' => $bomId,
            'part_id_fk' => $p1->part_id,
            'element_quantity' => 7,
        ]);

        $this->assertDatabaseHas('bom_elements', [
            'bom_id_fk' => $bomId,
            'part_id_fk' => $p2->part_id,
            'element_quantity' => 3,
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
}
