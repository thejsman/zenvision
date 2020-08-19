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
            $table->integer('order_number');
            $table->timestamp('created_on_shopify');
            $table->boolean('test');
            $table->decimal('total_price', 8, 2);
            $table->decimal('total_tax', 6, 2);
            $table->boolean('taxes_included')->nullable()->default(false); // Yellow
            $table->string('currency');
            $table->string('financial_status');
            $table->boolean('confirmed')->nullable()->default(true); // Yellow
            $table->decimal('total_discounts', 6,2);
            $table->string('referring_site')->nullable();
            $table->string('landing_site')->nullable();
            $table->string('cancelled_at')->nullable();
            $table->decimal('total_price_usd',8,2);
            $table->string('source_identifier')->nullable(); //yellow
            $table->string('source_url')->nullable(); //yellow
            $table->json('discount_applications')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->json('tax_lines');
            $table->json('refunds')->nullable();
            $table->decimal('total_tip_received', 4, 2);
            $table->string('original_total_duties_set')->nullable();
            $table->string('current_total_duties_set')->nullable();
            $table->json('shipping_lines')->nullable();
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
