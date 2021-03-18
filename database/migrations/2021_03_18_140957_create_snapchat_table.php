<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnapchatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snapchat_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('snapchat_user_id');
            $table->string('organization_id');
            $table->string('display_name')->nullable();
            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('snapchat_email')->nullable();
            $table->boolean('isDeleted')->default(false);
            $table->boolean('enabled_on_dashboard')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->string('member_status')->nullable();
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
        Schema::dropIfExists('snapchat_ads');
    }
}
