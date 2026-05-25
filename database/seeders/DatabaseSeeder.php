<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tự động gọi ProductSeeder chứa 35 xe máy để nạp vào DB
        $this->call([
            ProductSeeder::class,
        ]);
    }
}