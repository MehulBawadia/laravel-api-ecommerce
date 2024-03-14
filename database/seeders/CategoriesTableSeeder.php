<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = $this->dummyData();

        Category::insert($categories);
    }

    private function dummyData(): array
    {
        $appName = config('app.name');
        $nowTime = now();

        return [
            0 => [
                'name' => 'Workwear Chic',
                'slug' => 'workwear-chic',
                'description' => 'Dress to impress and empower. Find power suits, elegant dresses, and sharp separates for the modern professional woman.',
                'meta_title' => 'Elevate Your Work Style: Shop Workwear Chic for Women | '.$appName,
                'meta_description' => 'Discover a powerful collection of workwear essentials designed for the confident Indian woman. Shop stylish suits, dresses, and separates that make a statement.',
                'meta_keywords' => "workwear for women India, office wear India, professional attire, Indian women's suits, power dressing",
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            1 => [
                'name' => 'Ethnic Elegance',
                'slug' => 'ethnic-elegance',
                'description' => 'Embrace your heritage in style. Explore a collection of stunning sarees, lehengas, anarkalis, and kurtas, perfect for weddings, festivals, and special occasions.',
                'meta_title' => 'Celebrate Tradition in Style: Shop Ethnic Wear for Women | '.$appName,
                'meta_description' => 'Discover a curated collection of exquisite sarees, lehengas, anarkalis, and kurtas for the modern Indian woman. Find the perfect outfit to celebrate your heritage in style.',
                'meta_keywords' => 'Indian ethnic wear, sarees online, lehengas for women, anarkali suits, Indian wedding wear',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            2 => [
                'name' => 'Comfy Casuals',
                'slug' => 'comfy-casuals',
                'description' => 'Effortless style for everyday comfort. Discover a range of cozy tees, relaxed-fit jeans, comfy dresses, and trendy sweaters for a laid-back look.',
                'meta_title' => 'Dress for Comfort: Shop Everyday Casuals for Women | '.$appName,
                'meta_description' => 'Explore a range of comfortable and stylish casual wear for the Indian woman. Shop tees, sweaters, jeans, and dresses for everyday ease.',
                'meta_keywords' => "casual wear for women India, comfortable clothing online, relaxed fit clothes, everyday dresses, Indian women's jeans",
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            3 => [
                'name' => 'Activewear Essentials',
                'slug' => 'activewear-essentials',
                'description' => 'Gear up for any workout with confidence. Find high-performance clothing like yoga pants, sports bras, activewear sets, and gym accessories.',
                'meta_title' => 'Move Freely: Shop Activewear for Women | '.$appName,
                'meta_description' => 'Discover a collection of high-quality activewear designed for the modern Indian woman. Shop yoga wear, gym clothes, and workout essentials for any fitness activity.',
                'meta_keywords' => 'activewear for women India, sports bras online, yoga pants for women, gym wear India, workout clothes',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            4 => [
                'name' => 'Weekend Vibes',
                'slug' => 'weekend-vibes',
                'description' => 'Dress for relaxation and leisure in style. Explore trendy jumpsuits, stylish skirts, breezy tops, and comfortable dresses for your weekend adventures.',
                'meta_title' => 'Style Your Weekends: Shop Weekend Wear for Women | '.$appName,
                'meta_description' => "Discover a collection of trendy and comfortable outfits perfect for the Indian woman's weekend. Shop jumpsuits, skirts, tops, and dresses for stylish relaxation.",
                'meta_keywords' => "weekend wear for women India, trendy jumpsuits online, stylish skirts for women, comfortable dresses, Indian women's casual wear",
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            5 => [
                'name' => 'Denim Delights',
                'slug' => 'denim-delights',
                'description' => 'A timeless wardrobe staple. Find a variety of denim styles like jeans, jackets, skirts, and shorts to elevate your everyday look.',
                'meta_title' => 'Elevate Your Look: Shop Denim Clothing for Women | '.$appName,
                'meta_description' => 'Explore a wide range of trendy denim clothing for the modern Indian woman. Shop jeans, jackets, skirts, and shorts for all occasions.',
                'meta_keywords' => "denim clothing for women India, jeans online India, designer denim jackets, denim skirts for women, women's denim shorts",
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
        ];
    }
}
