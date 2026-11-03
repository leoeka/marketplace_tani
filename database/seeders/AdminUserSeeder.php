<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = trim((string) config('admin.email'));
        $password = (string) config('admin.password');

        if ($email === '' || $password === '') {
            throw new RuntimeException('Admin seeding requires both ADMIN_EMAIL and ADMIN_PASSWORD to be set in the .env file.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Master Admin',
                'password' => $password,
                'role' => 'admin',
                'customer_type' => 'individual',
            ],
        );
    }
}
