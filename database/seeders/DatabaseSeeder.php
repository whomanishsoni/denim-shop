<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create customer user
        User::create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // Create sample products
        $products = [
            [
                'name' => 'Denim Jacket Vintage',
                'description' => 'A vintage-style denim jacket with a classic cut. Features traditional button closure and two chest pockets. Perfect for layering.',
                'price' => 129.99,
                'stock' => 30,
                'category' => 'Jackets',
                'images' => [
                    'storage/images/1.jpeg',
                    'storage/images/1.jpeg'
                ]
            ],
            [
                'name' => 'Denim Button-Up Shirt',
                'description' => 'Soft and comfortable denim shirt with button-up design. Made from lightweight denim fabric, perfect for casual occasions.',
                'price' => 69.99,
                'stock' => 40,
                'category' => 'Shirts',
                'images' => [
                    'storage/images/2.jpeg',
                    'storage/images/2.jpeg'
                ]
            ],
            [
                'name' => 'Skinny Fit Jeans',
                'description' => 'Modern skinny fit jeans with stretch fabric for comfort and style. Features a mid-rise waist and tapered leg.',
                'price' => 99.99,
                'stock' => 45,
                'category' => 'Jeans',
                'images' => [
                    'storage/images/3.jpeg',
                    'storage/images/3.jpeg'
                ]
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}