<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_settings_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/user-settings');

        $response->assertStatus(200);
        $response->assertSee('User Settings'); // just some text from the page
    }

    public function test_timezone_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/user/settings', [
            'timezone' => 'Europe/Berlin',
        ]);

        $response->assertRedirect(); // adjust as needed
        $this->assertDatabaseHas('user_settings', [
            'user_id_fk' => $user->id,
            'setting_name' => 'timezone',
            'setting_value' => 'Europe/Berlin',
        ]);
        
    }
}
