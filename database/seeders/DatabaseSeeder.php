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
                'name' => 'Classic Blue Jeans',
                'description' => 'Comfortable and stylish classic blue jeans made from premium denim. Perfect for everyday wear with a timeless design that never goes out of style.',
                'price' => 89.99,
                'stock' => 50,
                'category' => 'Jeans',
                'images' => [
                    'https://images.pexels.com/photos/1598505/pexels-photo-1598505.jpeg',
                    'https://images.pexels.com/photos/1598507/pexels-photo-1598507.jpeg'
                ]
            ],
            [
                'name' => 'Denim Jacket Vintage',
                'description' => 'A vintage-style denim jacket with a classic cut. Features traditional button closure and two chest pockets. Perfect for layering.',
                'price' => 129.99,
                'stock' => 30,
                'category' => 'Jackets',
                'images' => [
                    'https://images.pexels.com/photos/1036627/pexels-photo-1036627.jpeg',
                    'https://images.pexels.com/photos/1152994/pexels-photo-1152994.jpeg'
                ]
            ],
            [
                'name' => 'Denim Button-Up Shirt',
                'description' => 'Soft and comfortable denim shirt with button-up design. Made from lightweight denim fabric, perfect for casual occasions.',
                'price' => 69.99,
                'stock' => 40,
                'category' => 'Shirts',
                'images' => [
                    'https://images.pexels.com/photos/1040945/pexels-photo-1040945.jpeg',
                    'https://images.pexels.com/photos/996329/pexels-photo-996329.jpeg'
                ]
            ],
            [
                'name' => 'Denim Shorts Classic',
                'description' => 'Comfortable denim shorts with a classic fit. Perfect for summer days with a 5-inch inseam and traditional 5-pocket design.',
                'price' => 49.99,
                'stock' => 35,
                'category' => 'Shorts',
                'images' => [
                    'https://images.pexels.com/photos/1926769/pexels-photo-1926769.jpeg',
                    'https://images.pexels.com/photos/1598508/pexels-photo-1598508.jpeg'
                ]
            ],
            [
                'name' => 'Denim Vest Modern',
                'description' => 'Stylish denim vest with a modern cut. Features button closure and two side pockets. Perfect for layering over shirts.',
                'price' => 79.99,
                'stock' => 25,
                'category' => 'Vests',
                'images' => [
                    'https://images.pexels.com/photos/1040945/pexels-photo-1040945.jpeg',
                    'https://images.pexels.com/photos/1152994/pexels-photo-1152994.jpeg'
                ]
            ],
            [
                'name' => 'Skinny Fit Jeans',
                'description' => 'Modern skinny fit jeans with stretch fabric for comfort and style. Features a mid-rise waist and tapered leg.',
                'price' => 99.99,
                'stock' => 45,
                'category' => 'Jeans',
                'images' => [
                    'https://images.pexels.com/photos/1598507/pexels-photo-1598507.jpeg',
                    'https://images.pexels.com/photos/1598505/pexels-photo-1598505.jpeg'
                ]
            ],
            [
                'name' => 'Oversized Denim Jacket',
                'description' => 'Trendy oversized denim jacket with a relaxed fit. Perfect for creating layered looks with its roomy design.',
                'price' => 149.99,
                'stock' => 20,
                'category' => 'Jackets',
                'images' => [
                    'https://images.pexels.com/photos/1152994/pexels-photo-1152994.jpeg',
                    'https://images.pexels.com/photos/1036627/pexels-photo-1036627.jpeg'
                ]
            ],
            [
                'name' => 'Chambray Work Shirt',
                'description' => 'Lightweight chambray shirt with a work-inspired design. Features chest pockets and durable construction.',
                'price' => 79.99,
                'stock' => 30,
                'category' => 'Shirts',
                'images' => [
                    'https://images.pexels.com/photos/996329/pexels-photo-996329.jpeg',
                    'https://images.pexels.com/photos/1040945/pexels-photo-1040945.jpeg'
                ]
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}