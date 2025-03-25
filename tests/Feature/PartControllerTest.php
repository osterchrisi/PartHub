<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Footprint;
use App\Models\Part;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Location;
use App\Models\StockLevel;
use App\Models\SupplierData;
use App\Models\AlternativeGroup;
use App\Models\PartUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_parts_view_can_be_rendered()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create related data
        $category = Category::factory()->create();
        $footprint = Footprint::factory()->create();
        $supplier = Supplier::factory()->create();

        // Look at the weird shit the factory comes up with
        // dump($category->toArray());
        // dump($footprint->toArray());
        // dump($supplier->toArray());

        $response = $this->get(route('parts'));

        $response->assertStatus(200);
        $response->assertViewIs('parts.parts');
        $response->assertViewHas(['parts', 'categories', 'footprints', 'suppliers']);
    }

    public function test_parts_table_partial_can_be_rendered()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('parts.partsTable'));

        $response->assertStatus(200);
        $response->assertViewIs('parts.partsTable');
        $response->assertViewHas(['parts', 'categories']);
    }

    public function test_show_part_view_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create dependencies
        $location = Location::factory()->create();
        $category = Category::factory()->create(['part_category_owner_u_fk' => $user->id]);
        $footprint = Footprint::factory()->create(['footprint_owner_u_fk' => $user->id]);
        $supplier = Supplier::factory()->create(['supplier_owner_u_fk' => $user->id]);
        $unit = PartUnit::factory()->create();


        // Create part
        $part = Part::factory()->create([
            'part_owner_u_fk' => $user->id,
            'part_category_fk' => $category->category_id,
            'part_footprint_fk' => $footprint->footprint_id,
            'part_unit_fk' => $unit->unit_id,
        ]);

        // Create related stock level
        StockLevel::factory()->create([
            'part_id_fk' => $part->part_id,
            'location_id_fk' => $location->location_id,
        ]);

        // Create related supplier data
        SupplierData::factory()->create([
            'part_id_fk' => $part->part_id,
            'supplier_id_fk' => $supplier->supplier_id,
            'supplier_data_owner_u_fk' => $user->id,
        ]);

        // Create alternative group and attach part
        $altGroup = AlternativeGroup::factory()->create(['owner_u_fk' => $user->id]);
        $altGroup->alternativeParts()->attach($part->part_id);

        // Call the show route
        $response = $this->get('/part/' . $part->part_id);

        // Assertions
        $response->assertOk();
        $response->assertViewIs('parts.showPart');
        $response->assertViewHas(['part', 'stock_levels', 'supplierData', 'alternativeData']);
    }

    public function test_part_can_be_created_with_stock_and_supplier_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create(['part_category_owner_u_fk' => $user->id]);
        $footprint = Footprint::factory()->create(['footprint_owner_u_fk' => $user->id]);
        $supplier = Supplier::factory()->create(['supplier_owner_u_fk' => $user->id]);
        $location = Location::factory()->create(['location_owner_u_fk' => $user->id]);

        $data = [
            'part_name' => 'SuperCapacitor',
            'description' => 'Stores energy like a beast',
            'comment' => 'Test comment',
            'footprint' => $footprint->footprint_id,
            'category' => $category->category_id,
            'quantity' => 50,
            'to_location' => $location->location_id,
            'min_quantity' => 10,
            'suppliers' => [
                [
                    'supplier_id' => $supplier->supplier_id,
                    'URL' => 'https://supplier.com/supercap',
                    'SPN' => 'CAP-9000',
                    'price' => 0.99,
                ],
            ],
        ];

        $response = $this->postJson('/part.create', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['Part ID', 'Stock Entry ID']);

        $this->assertDatabaseHas('parts', [
            'part_name' => 'SuperCapacitor',
            'part_owner_u_fk' => $user->id,
        ]);

        $this->assertDatabaseHas('stock_levels', [
            'stock_level_quantity' => 50,
            'location_id_fk' => $location->location_id,
        ]);

        $this->assertDatabaseHas('supplier_data', [
            'part_id_fk' => $response['Part ID'],
            'supplier_id_fk' => $supplier->supplier_id,
        ]);
    }

}
