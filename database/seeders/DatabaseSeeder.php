<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public static array $seeders = [];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (self::$seeders as $seeder) {
            $this->call($seeder);
        }
    }
}
