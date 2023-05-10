<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::upsert([
            ['name' => 'Особисті'],
            ['name' => 'Робота'],
            ['name' => 'Друзі'],
            ['name' => 'Сім\'я'],
            ['name' => 'Подорожі'],
            ['name' => 'Свята'],
            ['name' => 'Спорт'],
        ], ['name']);
    }
}
