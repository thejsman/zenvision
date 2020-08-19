<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->bigInteger('variant_id');
            $table->string('title');
            $table->integer('quantity');
            $table->string('sku');
            $table->string('variant_title')->nullable();
            $table->string('fulfillment_service');
            $table->bigInteger('product_id');
            $table->json('properties')->nullable();
            $table->decimal('price',8,2);
            $table->decimal('total_discount', 8,2);
            $table->string('fulfillment_status')->nullable();
            $table->json('discount_allocations')->nullable();  //yellow
            $table->json('duties')->nullable();
            $table->json('tax_lines');
            $table->timestamps();

            // Foreign Key
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
        Schema::dropIfExists('line_items');
    }
}
