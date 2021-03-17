<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ad_account_id')->nullable();
            $table->string('ad_account_name')->nullable();
            $table->string('access_token')->nullable();
            $table->string('currency')->nullable();
            $table->boolean('isDeleted')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('enabled_on_dashboard')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facebook_ads');
    }
}
