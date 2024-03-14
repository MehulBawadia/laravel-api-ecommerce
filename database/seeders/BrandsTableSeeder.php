<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() : void
    {
        $brands = $this->dummyData();

        Brand::insert($brands);
    }

    private function dummyData() : array
    {
        $nowTime = now();

        return [
            0 => [
                "name" => "Fabcurate",
                "slug" => "fabcurate",
                "description" => "Designed for the modern Indian woman, Fabcurate offers a fusion of contemporary style and Indian heritage.",
                "meta_title" => "Discover your unique style: Shop Fabcurate - Fashion for the Modern Indian Woman",
                "meta_description" => "Explore a collection of trendy and versatile clothing designed for the confident Indian woman. Find stylish dresses, tops, jumpsuits, and more at Fabcurate.",
                "meta_keywords" => "Indian women's clothing online, contemporary Indian fashion, trendy dresses for women, stylish tops India, comfortable jumpsuits",
                "created_at" => $nowTime,
                "updated_at" => $nowTime,
            ],
            1 => [
                "name" => "Rang Rasiya",
                "slug" => "rang-rasiya",
                "description" => "Embrace the vibrant colors and exquisite craftsmanship of India. Rang Rasiya offers a range of ethnic wear perfect for weddings, festivals, and special occasions.",
                "meta_title" => "Celebrate in Style: Shop Ethnic Wear for Women | Rang Rasiya",
                "meta_description" => "Immerse yourself in the rich cultural heritage of India with Rang Rasiya. Discover stunning sarees, lehengas, anarkalis, and kurtas for a touch of timeless elegance.",
                "meta_keywords" => "Indian ethnic wear online, designer sarees India, festive lehengas for women, Anarkali suits for weddings, handcrafted Indian clothing",
                "created_at" => $nowTime,
                "updated_at" => $nowTime,
            ],
            2 => [
                "name" => "Boho Babe",
                "slug" => "boho-babe",
                "description" => "Express your free spirit with Boho Babe's collection of relaxed silhouettes, bold prints, and playful accessories.",
                "meta_title" => "Embrace your Boho Chic Style: Shop Boho Babe for Women",
                "meta_description" => "Discover a world of effortless style and vibrant energy with Boho Babe. Explore flowy dresses, statement jewelry, and unique accessories to unleash your inner bohemian spirit.",
                "meta_keywords" => "Boho chic clothing India, online shop for bohemian dresses, statement jewelry for women, trendy accessories online, comfortable and stylish clothes",
                "created_at" => $nowTime,
                "updated_at" => $nowTime,
            ],
            3 => [
                "name" => "The Active Woman",
                "slug" => "the-active-woman",
                "description" => "Designed for the woman on the go, The Active Woman offers high-performance activewear and athleisure apparel to support your fitness journey.",
                "meta_title" => "Move with Confidence: Shop Activewear for Women | The Active Woman",
                "meta_description" => "Discover a collection of high-quality activewear designed to empower your workouts. Shop yoga pants, sports bras, gym wear, and more at The Active Woman.",
                "meta_keywords" => "activewear for women India, sports bras online India, yoga wear for women, gym clothes India, fitness apparel online",
                "created_at" => $nowTime,
                "updated_at" => $nowTime,
            ],
            4 => [
                "name" => "Curve Love",
                "slug" => "curve-love",
                "description" => "Celebrate your curves with confidence. Curve Love offers a range of stylish and comfortable plus-size clothing designed for all body types.",
                "meta_title" => "Find your perfect fit: Shop Plus Size Clothing for Women | Curve Love",
                "meta_description" => "Embrace your unique beauty and discover a collection of flattering plus-size clothing designed to make you feel confident and stylish. Shop dresses, tops, pants, and more at Curve Love.",
                "meta_keywords" => "plus size clothing India online, clothes for curvy women, trendy plus size dresses, comfortable pants for women, stylish tops for plus size",
                "created_at" => $nowTime,
                "updated_at" => $nowTime,
            ],
            5 => [
                "name" => "Indegenous",
                "slug" => "indegenous",
                "description" => "Embrace the essence of India with handwoven textiles and sustainable fashion practices. Indigenous offers unique and eco-conscious clothing for the conscious consumer.",
                "meta_title" => "Shop Sustainable Fashion: Explore Eco-Friendly Clothing for Women | Indigenous",
                "meta_description" => "Discover a collection of beautiful and sustainable clothing made with ethically sourced materials and traditional Indian craftsmanship. Shop dresses, kurtas, and accessories at Indigenous.",
                "meta_keywords" => "sustainable clothing India, eco-friendly fashion for women, handwoven textiles India, ethical fashion online, unique Indian clothing",
                "created_at" => $nowTime,
                "updated_at" => $nowTime,
            ],
            6 => [
                "name" => "Style Fiesta",
                "slug" => "style-fiesta",
                "description" => "Your one-stop shop for the latest trends. Style Fiesta offers a curated selection of fashionable clothing and accessories to keep you looking your best.",
                "meta_title" => "Dress to Impress: Shop Trendy Clothing for Women | Style Fiesta",
                "meta_description" => "Explore the latest fashion trends and discover a variety of stylish clothing and accessories for every occasion. Shop dresses, tops, shoes, and bags at Style Fiesta.",
                "meta_keywords" => "trendy clothing for women India, latest fashion online India, stylish dresses online, fashionable accessories for women, shop clothes online India",
                "created_at" => $nowTime,
                "updated_at" => $nowTime,
            ]
        ];
    }
}
