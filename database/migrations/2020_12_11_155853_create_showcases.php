<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowcases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('showcases', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('subheading')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('privacy_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('art_channel_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('showcases', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('art_channel_id')->references('id')->on('art_channels');
            $table->foreign('privacy_id')->references('id')->on('privacies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('showcases');
    }
}
