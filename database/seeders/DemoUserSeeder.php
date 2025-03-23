<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'hello@parthub.online'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
            ]
        );
    }
}
