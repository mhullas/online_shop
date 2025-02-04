<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $title = fake()->unique()->name();
        $slug = Str::slug($title);

        $subCategories = [7,11];
        $subRandKey = array_rand($subCategories);

        $brands = [5,6,7,8,9];
        $brandRandKey = array_rand($brands);

        $categories = [10, 14, 17];
        $categoryRandKey = array_rand($categories);

        return [
            'title' => $title,
            'slug' => $slug,
            'category_id' => $categories[$categoryRandKey],
            'sub_category_id' => $subCategories[$subRandKey],
            'brand_id' => $brands[$brandRandKey],
            'price' => rand(10, 10000),
            'sku' => rand(1000, 10000),
            'qty' => 10,
            'is_featured' => 'Yes',
            'status' => 1,
        ];
    }
}
