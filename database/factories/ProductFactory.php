<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Menghubungkan produk secara acak ke salah satu ID Category yang ada
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            
            // Membuat kode SKU unik untuk setiap produk sembako
            'sku' => 'SKU-' . $this->faker->unique()->numberBetween(1000, 9999),
            
            // Nama produk sembako (tanpa unique agar bisa berulang jika jumlahnya banyak)
            'name' => $this->faker->randomElement([
                'Beras Ramos 5kg', 'Minyak Goreng 2L', 'Gula Pasir 1kg', 
                'Tepung Terigu 1kg', 'Kecap Manis 500ml', 'Telur Ayam 1kg', 
                'Garam Halus', 'Susu Kental Manis', 'Kopi Bubuk 200g', 'Teh Celup Kotak'
            ]),
            
            // Harga acak antara Rp10.000 sampai Rp100.000
            'price' => $this->faker->numberBetween(10, 100) * 1000,
            
            // Stok barang di gudang antara 5 sampai 100
            'stock' => $this->faker->numberBetween(5, 100),
        ];
    }
}