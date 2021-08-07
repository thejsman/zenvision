<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_order_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('line_item_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_number');
            $table->bigInteger('variant_id');
            $table->string('title');
            $table->integer('quantity');
            $table->string('sku')->nullable()->default('no_sku');
            $table->string('variant_title')->nullable();
            $table->string('fulfillment_service');
            $table->bigInteger('product_id');
            $table->decimal('price', 8, 2);
            $table->decimal('total_cost')->nullable();
            $table->decimal('total_discount', 8, 2);
            $table->string('fulfillment_status')->nullable();
            $table->json('duties')->nullable();
            $table->json('tax_lines')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('shopify_stores')->onDelete('cascade');
            $table->foreign('order_id')->references('order_id')->on('shopify_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopify_order_products');
    }
}
