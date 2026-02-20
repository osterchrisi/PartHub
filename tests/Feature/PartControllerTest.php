<?php

namespace Tests\Feature;

use App\Models\AlternativeGroup;
use App\Models\Category;
use App\Models\Footprint;
use App\Models\Location;
use App\Models\Part;
use App\Models\PartUnit;
use App\Models\StockLevel;
use App\Models\Supplier;
use App\Models\SupplierData;
use App\Models\User;
use App\Models\UserSetting;
use App\Services\MouserApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
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
        $altGroup->parts()->attach($part->part_id);

        // Call the show route
        $response = $this->get('/part/'.$part->part_id);

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

    public function test_mouser_search_returns_403_if_disabled(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_enabled'],
            ['setting_value' => 'false']
        );

        $this->get('/search-mouser-part/asdf')
            ->assertStatus(403)
            ->assertJson(['message' => 'Mouser search is disabled in your settings.']);
    }

    public function test_mouser_search_returns_403_if_no_api_key_set(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_enabled'],
            ['setting_value' => 'true']
        );

        // ensure empty / missing key
        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_api_key'],
            ['setting_value' => '']
        );

        $this->get('/search-mouser-part/asdf')
            ->assertStatus(403)
            ->assertJson(['message' => 'No Mouser API key set.']);
    }

    public function test_mouser_search_returns_422_with_rewritten_message_if_api_key_invalid(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_enabled'],
            ['setting_value' => 'true']
        );

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_api_key'],
            ['setting_value' => Crypt::encryptString('invalid')]
        );

        $this->mock(MouserApiService::class, function ($mock) {
            $mock->shouldReceive('searchPartNumber')
                ->once()
                ->andReturn([
                    'Errors' => [
                        [
                            'Code' => 'Invalid',
                            'Message' => 'Invalid unique identifier.',
                            'PropertyName' => 'API Key',
                            'ResourceKey' => 'InvalidIdentifier',
                        ],
                    ],
                ]);
        });

        $this->get('/search-mouser-part/asdf')
            ->assertStatus(422)
            ->assertJson(['message' => 'Your Mouser API key is invalid. Please check it in User Settings.']);
    }

    public function test_mouser_search_returns_422_with_original_message_for_other_mouser_errors(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_enabled'],
            ['setting_value' => 'true']
        );

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_api_key'],
            ['setting_value' => Crypt::encryptString('validkey')]
        );

        $this->mock(MouserApiService::class, function ($mock) {
            $mock->shouldReceive('searchPartNumber')
                ->once()
                ->andReturn([
                    'Errors' => [
                        [
                            'Message' => 'Some other Mouser error.',
                            'PropertyName' => 'mouserPartNumber',
                        ],
                    ],
                ]);
        });

        $this->get('/search-mouser-part/asdf')
            ->assertStatus(422)
            ->assertJson(['message' => 'Some other Mouser error.']);
    }

    public function test_mouser_search_returns_502_if_service_throws(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_enabled'],
            ['setting_value' => 'true']
        );

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_api_key'],
            ['setting_value' => Crypt::encryptString('validkey')]
        );

        $this->mock(MouserApiService::class, function ($mock) {
            $mock->shouldReceive('searchPartNumber')
                ->once()
                ->andThrow(new \Exception('boom'));
        });

        $this->get('/search-mouser-part/asdf')
            ->assertStatus(502)
            ->assertJson(['message' => 'Mouser request failed. Check your API key and try again.']);
    }

    public function test_mouser_search_returns_results_on_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_enabled'],
            ['setting_value' => 'true']
        );

        UserSetting::updateOrCreate(
            ['user_id_fk' => $user->id, 'setting_name' => 'mouser_api_key'],
            ['setting_value' => Crypt::encryptString('validkey')]
        );

        $this->mock(MouserApiService::class, function ($mock) {
            $mock->shouldReceive('searchPartNumber')
                ->once()
                ->andReturn([
                    'Errors' => [],
                    'SearchResults' => [
                        'NumberOfResult' => 1,
                        'Parts' => [
                            ['ManufacturerPartNumber' => 'LM324KAPWR'],
                        ],
                    ],
                ]);
        });

        $this->get('/search-mouser-part/lm324')
            ->assertOk()
            ->assertJsonPath('SearchResults.NumberOfResult', 1);
    }

    public function test_mouser_api_key_cannot_be_empty(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/settings/mouser_api_key', [
            'value' => '',
        ])->assertStatus(422)
            ->assertJson(['message' => 'API key cannot be empty']);
    }
}
