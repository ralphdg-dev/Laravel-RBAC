<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'description' => 'Posts about technology, programming, and software development',
                'slug' => 'technology'
            ],
            [
                'name' => 'Lifestyle',
                'description' => 'Posts about lifestyle, health, and personal development',
                'slug' => 'lifestyle'
            ],
            [
                'name' => 'Business',
                'description' => 'Posts about business, entrepreneurship, and finance',
                'slug' => 'business'
            ],
            [
                'name' => 'Education',
                'description' => 'Educational content and learning resources',
                'slug' => 'education'
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Entertainment, movies, music, and pop culture',
                'slug' => 'entertainment'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
