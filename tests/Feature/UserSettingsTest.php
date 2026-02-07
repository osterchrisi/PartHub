<?php

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
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

    public function test_mouser_enabled_can_be_toggled_via_settings_endpoint(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/settings/mouser_enabled', [
            'value' => 'true',
        ])->assertOk();

        $this->assertDatabaseHas('user_settings', [
            'user_id_fk' => $user->id,
            'setting_name' => 'mouser_enabled',
            'setting_value' => 'true',
        ]);
    }

    public function test_mouser_api_key_can_be_saved_and_is_encrypted(): void
    {
        $user = User::factory()->create();

        $plain = 'aaa';

        $this->actingAs($user)->post('/settings/mouser_api_key', [
            'value' => $plain,
        ])->assertOk();

        $row = UserSetting::where('user_id_fk', $user->id)
            ->where('setting_name', 'mouser_api_key')
            ->firstOrFail();

        $this->assertNotSame($plain, $row->setting_value);
        $this->assertSame($plain, Crypt::decryptString($row->setting_value));
    }

    public function test_mouser_api_key_cannot_be_fetched_via_get_endpoint(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/settings/mouser_api_key')
            ->assertStatus(403);
    }
}
