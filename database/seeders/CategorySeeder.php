<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Beras & Biji-bijian',
            'Minyak & Lemak',
            'Gula & Garam',
            'Tepung & Bumbu',
            'Minuman',
            'Snack & Makanan Ringan',
            'Sabun & Kebersihan',
            'Lain-lain',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
