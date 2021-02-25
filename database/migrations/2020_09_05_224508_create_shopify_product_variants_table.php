<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->bigInteger('product_id');
            $table->bigInteger('variant_id');
            $table->bigInteger('inventory_item_id')->nullable();
            $table->string('product_title');
            $table->string('variant_title')->nullable();
            $table->string('sku')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('sales_price');
            $table->decimal('cost')->nullable();
            $table->decimal('shipping_cost')->nullable();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('shopify_stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopify_product_variants');
    }
}
