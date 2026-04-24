<?php
namespace Database\Seeders;
use App\Models\{User, Category, Brand, Product};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@motorbike.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Danh mục
        $cats = ['Xe số', 'Xe tay ga', 'Xe côn tay', 'Xe điện'];
        foreach ($cats as $cat) {
            Category::create(['name' => $cat, 'slug' => Str::slug($cat)]);
        }

        // Hãng xe
        $brands = ['Honda', 'Yamaha', 'SYM', 'Piaggio', 'Suzuki'];
        foreach ($brands as $brand) {
            Brand::create(['name' => $brand, 'slug' => Str::slug($brand)]);
        }

        // Xe mẫu
        $products = [
            ['name' => 'Honda Wave Alpha 110', 'price' => 18490000, 'category_id' => 1, 'brand_id' => 1, 'stock' => 10],
            ['name' => 'Honda Vision 110', 'price' => 30490000, 'category_id' => 2, 'brand_id' => 1, 'stock' => 8],
            ['name' => 'Yamaha Exciter 155', 'price' => 52990000, 'category_id' => 3, 'brand_id' => 2, 'stock' => 5],
            ['name' => 'Honda Air Blade 160', 'price' => 52190000, 'category_id' => 2, 'brand_id' => 1, 'stock' => 12],
            ['name' => 'Yamaha Grande 125', 'price' => 46990000, 'category_id' => 2, 'brand_id' => 2, 'stock' => 7],
            ['name' => 'SYM Star SR 170', 'price' => 27990000, 'category_id' => 1, 'brand_id' => 3, 'stock' => 15],
        ];
        foreach ($products as $p) {
            Product::create(array_merge($p, ['slug' => Str::slug($p['name']).'-'.rand(100,999), 'is_active' => true]));
        }
    }
}