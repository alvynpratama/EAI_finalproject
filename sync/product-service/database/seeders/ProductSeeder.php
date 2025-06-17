<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Sushi', 'description' => 'Nasi gulung dengan ikan mentah dan rumput laut', 'price' => 28000, 'stock' => 20],
            ['name' => 'Ramen', 'description' => 'Mi kuah khas Jepang dengan kaldu gurih', 'price' => 32000, 'stock' => 15],
            ['name' => 'Tempura', 'description' => 'Udang goreng tepung renyah', 'price' => 25000, 'stock' => 18],
            ['name' => 'Okonomiyaki', 'description' => 'Pancake gurih berisi kol dan topping', 'price' => 27000, 'stock' => 10],
            ['name' => 'Takoyaki', 'description' => 'Bola-bola gurita dengan saus manis-asin', 'price' => 20000, 'stock' => 25],
            ['name' => 'Katsudon', 'description' => 'Nasi dengan irisan daging goreng dan telur', 'price' => 30000, 'stock' => 12],
            ['name' => 'Onigiri', 'description' => 'Nasi kepal isi tuna atau salmon', 'price' => 12000, 'stock' => 30],
            ['name' => 'Gyudon', 'description' => 'Nasi dengan irisan daging sapi dan bawang', 'price' => 31000, 'stock' => 15],
            ['name' => 'Yakisoba', 'description' => 'Mi goreng Jepang dengan saus khas', 'price' => 26000, 'stock' => 20],
            ['name' => 'Miso Soup', 'description' => 'Sup miso dengan tahu dan rumput laut', 'price' => 8000, 'stock' => 40],
            ['name' => 'Udon', 'description' => 'Mi tebal dengan kuah kaldu ringan', 'price' => 29000, 'stock' => 14],
            ['name' => 'Shabu-Shabu', 'description' => 'Irisan daging tipis direbus dalam kuah panas', 'price' => 45000, 'stock' => 8],
            ['name' => 'Tonkatsu', 'description' => 'Daging babi goreng tepung krispi', 'price' => 31000, 'stock' => 16],
            ['name' => 'Chawanmushi', 'description' => 'Puding telur kukus lembut', 'price' => 15000, 'stock' => 22],
            ['name' => 'Matcha Ice Cream', 'description' => 'Es krim rasa teh hijau khas Jepang', 'price' => 12000, 'stock' => 35],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
