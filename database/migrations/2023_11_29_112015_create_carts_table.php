<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->restrictOnDelete();
            $table->string('product_name');
            $table->string('product_slug');
            $table->float('rate')->default(0.0);
            $table->unsignedInteger('quantity')->default(0);
            $table->float('amount')->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
