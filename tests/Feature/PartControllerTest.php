<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Footprint;
use App\Models\Part;
use App\Models\Supplier;
use App\Models\User;
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
}
