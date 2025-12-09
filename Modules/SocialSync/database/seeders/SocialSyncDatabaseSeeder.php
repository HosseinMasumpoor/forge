<?php

namespace Modules\SocialSync\database\seeders;

use Illuminate\Database\Seeder;

class SocialSyncDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->call([
             SocialAccountSeeder::class
         ]);
    }
}
