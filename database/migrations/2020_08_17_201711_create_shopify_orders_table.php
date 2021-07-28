<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('store_id');
            $table->bigInteger('order_id')->unique();
            $table->bigInteger('order_number');
            $table->timestamp('created_on_shopify');
            $table->decimal('total_price', 8, 2);
            $table->decimal('total_tax', 6, 2);
            $table->string('currency');
            $table->string('financial_status');
            $table->decimal('total_discounts', 6, 2);
            $table->string('referring_site')->nullable();
            $table->string('landing_site')->nullable();
            $table->string('cancelled_at')->nullable();
            $table->decimal('total_price_usd', 8, 2);
            $table->json('discount_applications')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->json('tax_lines')->nullable();
            $table->json('refunds')->nullable();
            $table->decimal('total_tip_received', 4, 2)->nullable();
            $table->string('original_total_duties_set')->nullable();
            $table->string('current_total_duties_set')->nullable();
            $table->string('shipping_country')->nullable();
            $table->json('shipping_lines')->nullable();
            $table->boolean('is_deleted')->nullable()->default(0);
            $table->timestamps();

            // Foreign keys
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
        Schema::dropIfExists('shopify_orders');
    }
}
