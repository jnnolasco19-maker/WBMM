<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Default entrypoint seeder.
 *
 * Usage:
 *   php spark db:seed DatabaseSeeder
 *
 * This delegates to WBMMSeeder, which mirrors schema.sql demo data.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(WBMMSeeder::class);
    }
}

