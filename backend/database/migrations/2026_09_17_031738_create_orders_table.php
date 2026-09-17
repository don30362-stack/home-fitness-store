<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 30)->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('purchaser_name', 50);
            $table->string('purchaser_phone', 20);
            $table->string('purchaser_email');

            $table->string('recipient_name', 50);
            $table->string('recipient_phone', 20);
            $table->string('postal_code', 10);
            $table->string('city', 20);
            $table->string('district', 30);
            $table->string('address');

            $table->string('shipping_method', 30)
                ->default('home_delivery');
            $table->decimal('shipping_fee', 10, 2)
                ->default(0);

            $table->string('payment_method', 30);
            $table->string('payment_status', 20)
                ->default('unpaid');
            $table->string('order_status', 20)
                ->default('pending');

            $table->decimal('subtotal', 10, 2);
            $table->decimal('total_amount', 10, 2);

            $table->string('logistics_company', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['order_status', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
