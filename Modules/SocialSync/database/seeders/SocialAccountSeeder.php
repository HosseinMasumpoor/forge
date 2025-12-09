<?php

namespace Modules\SocialSync\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\SocialSync\app\Enums\SocialProvider;
use Modules\SocialSync\app\Models\SocialAccount;
use Modules\User\Models\User;

class SocialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'user_id' => User::first()->id,
                'provider' => SocialProvider::INSTAGRAM,
                'provider_id' => "something",
                'name' => 'instagram1',
                'access_token' => Str::random(60),
                'refresh_token' => Str::random(60),
                'token_expires_at' => now()->addDays(30),
            ],
            [
                'user_id' => User::first()->id,
                'provider' => SocialProvider::TWITTER,
                'provider_id' => "something",
                'name' => 'twitter1',
                'access_token' => Str::random(60),
                'refresh_token' => Str::random(60),
                'token_expires_at' => now()->addDays(30),
            ],
        ];

        foreach ($accounts as $account) {
            SocialAccount::updateOrCreate([
                'name' => $account['name'],
            ], $account);
        }
    }
}

