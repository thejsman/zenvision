<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeBalanceTransactionsReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_balance_transactions_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('stripe_user_id');
            $table->string('balance_transaction_id');
            $table->timestamp('created');
            $table->timestamp('available_on');
            $table->string('currency');
            $table->decimal('gross');
            $table->decimal('fee');
            $table->decimal('net');
            $table->string('reporting_category');
            $table->text('description');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('stripe_user_id')->references('stripe_user_id')->on('stripe')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stripe_balance_transactions_reports');
    }
}
