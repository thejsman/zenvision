<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_reports', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_user_id')->nullable();
            $table->string('object_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('stripe_fee')->nullable();
            $table->string('report_type')->nullable();
            $table->string('report_url')->nullable();
            $table->boolean('report_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stripe_reports');
    }
}
