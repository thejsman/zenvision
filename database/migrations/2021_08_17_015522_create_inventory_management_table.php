<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_management', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->string('shopify_order_number')->nullable();
            $table->bigInteger('product_id');
            $table->bigInteger('variant_id');
            $table->bigInteger('inventory_item_id')->nullable();
            $table->string('product_title');
            $table->string('variant_title')->nullable();
            $table->string('sku')->nullable()->default('no_sku');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('sales_price');
            $table->decimal('cost')->nullable();
            $table->decimal('shipping_cost')->nullable();
            $table->decimal('units')->nullable();
            $table->decimal('total_inventory')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('inventory_management');
    }
}
