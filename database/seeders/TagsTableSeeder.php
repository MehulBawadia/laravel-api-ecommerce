<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = $this->dummyData();

        Tag::insert($tags);
    }

    /**
     * A dummy data for testing purposes.
     */
    private function dummyData(): array
    {
        $nowTime = now();

        return [
            0 => [
                'name' => 'Workwear',
                'slug' => 'workwear',
                'description' => 'Professional attire for the modern woman.',
                'meta_title' => 'Shop Workwear for Women | [Your Brand Name]',
                'meta_description' => 'Discover stylish & professional clothing for the confident woman. Shop work dresses, pantsuits & more.',
                'meta_keywords' => 'workwear India, office wear, professional attire, work dresses, pantsuits',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            1 => [
                'name' => 'Casual Wear',
                'slug' => 'casual-wear',
                'description' => 'Comfy & trendy styles for everyday life.',
                'meta_title' => 'Effortless Style: Shop Casual Wear for Women | [Your Brand Name]',
                'meta_description' => 'Explore comfy & trendy clothes for everyday wear. Shop tees, jeans, dresses & more for a relaxed look.',
                'meta_keywords' => 'casual wear India, everyday clothing, trendy tops, jeans, dresses',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            2 => [
                'name' => 'Activewear',
                'slug' => 'activewear',
                'description' => 'High-performance clothing for your workouts.',
                'meta_title' => 'Move Freely: Shop Activewear for Women | [Your Brand Name]',
                'meta_description' => 'High-quality activewear to support your fitness journey. Shop yoga wear, sports bras, gym clothes & more.',
                'meta_keywords' => 'activewear India, sports bras, yoga wear, gym clothes, workout clothes',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            3 => [
                'name' => 'Festive Wear',
                'slug' => 'festive-wear',
                'description' => 'Dazzling outfits for celebrations.',
                'meta_title' => 'Shine Bright: Shop Festive Wear for Women | [Your Brand Name]',
                'meta_description' => 'Discover a collection of festive wear. Shop dazzling kurtas, lehengas & jewelry to celebrate in style.',
                'meta_keywords' => 'festive wear India, Diwali clothes, lehengas, ethnic jewelry, festive collection',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            4 => [
                'name' => 'Plus Size',
                'slug' => 'plus-size',
                'description' => 'Flattering styles for all body types.',
                'meta_title' => 'Find Your Perfect Fit: Shop Plus Size Clothing | [Your Brand Name]',
                'meta_description' => 'Stylish & comfortable plus-size clothing designed to flatter your curves. Shop dresses, tops, pants & more.',
                'meta_keywords' => 'plus size clothing India, clothes for curvy women, trendy plus size, comfortable clothing, stylish tops',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            5 => [
                'name' => 'Nightwear & Loungewear',
                'slug' => 'nightwear-loungewear',
                'description' => 'Comfy and stylish essentials for relaxation at home.',
                'meta_title' => 'Unwind in Style: Shop Nightwear & Loungewear for Women | [Your Brand Name]',
                'meta_description' => 'Discover a comfortable and stylish collection of nightwear and loungewear for the modern woman. Shop pajamas, robes, and more for ultimate relaxation.',
                'meta_keywords' => 'nightwear India, loungewear for women, comfortable pajamas, sleepwear online, robes for women',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            6 => [
                'name' => 'Indian Accessories',
                'slug' => 'indian-accessories',
                'description' => 'Elevate your look with traditional Indian jewelry and accessories.',
                'meta_title' => 'Add a touch of Tradition: Shop Indian Accessories for Women | [Your Brand Name]',
                'meta_description' => 'Explore a collection of exquisite Indian jewelry and accessories to complement your ethnic wear. Shop earrings, necklaces, bangles, and more.',
                'meta_keywords' => 'Indian jewelry online, traditional earrings India, designer necklaces for women, bangles online India, ethnic accessories',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            7 => [
                'name' => 'Sustainable Fashion',
                'slug' => 'sustainable-fashion',
                'description' => 'Eco-friendly clothing choices for the conscious consumer.',
                'meta_title' => 'Shop Sustainable Fashion: Explore Eco-Conscious Clothing | [Your Brand Name]',
                'meta_description' => 'Discover a collection of beautiful and eco-friendly clothing made with sustainable materials and ethical practices. Shop dresses, tops, and more for a greener wardrobe.',
                'meta_keywords' => 'sustainable clothing India, eco-friendly fashion, ethical fashion online, organic cotton clothing, conscious consumerism',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            8 => [
                'name' => 'Winter Wear',
                'slug' => 'winter-wear',
                'description' => 'Stay warm and stylish in the colder months.',
                'meta_title' => 'Embrace the Winter: Shop Winter Wear for Women | [Your Brand Name]',
                'meta_description' => 'Explore a collection of cozy and stylish clothing perfect for the winter season. Shop sweaters, jackets, coats, and more to stay warm in style.',
                'meta_keywords' => 'winter wear India, sweaters for women, jackets online India, winter coats for women, stylish winter clothing',
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
            9 => [
                'name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Shoes, sandals, and more to complement your look.',
                'meta_title' => 'Step up your Style: Shop Footwear for Women | [Your Brand Name]',
                'meta_description' => 'Discover a wide variety of stylish and comfortable footwear for every occasion. Shop shoes, sandals, heels, and more to complete your look.',
                'meta_keywords' => "women's footwear India, shoes online India, sandals for women, heels online India, comfortable footwear",
                'created_at' => $nowTime,
                'updated_at' => $nowTime,
            ],
        ];
    }
}
