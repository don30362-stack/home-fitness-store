<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('product_code', 20)
                ->unique();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name', 150);

            $table->decimal('price', 10, 2);

            $table->string('short_description', 255)
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->unsignedInteger('stock')
                ->nullable();

            $table->unsignedInteger('low_stock_threshold')
                ->default(5);

            $table->string('status', 20)
                ->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
