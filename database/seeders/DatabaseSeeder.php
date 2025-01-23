<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $categories = Category::factory()->count(15)->create();

        $tags = Tag::factory()->count(20)->create();

        Product::factory()->count(100)->create()->each(function ($product) use ($categories, $tags) {

            $product->categories()->attach(
                $categories->random(rand(1, 5))->pluck('id')->toArray()
            );

            $product->tags()->attach(
                $tags->random(rand(1, 5))->pluck('id')->toArray()
            );
        });
    }
}
