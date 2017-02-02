<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create polls table
        Schema::create('polls', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('question');
	        $table->text('description')->nullable();
	        $table->boolean('show_results');
	        $table->unsignedInteger('user_id')->nullable();
	        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });

	    // Create poll options table
	    Schema::create("poll_options", function(Blueprint $table) {
		    $table->increments('id');
		    $table->unsignedInteger('poll_id');
		    $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
		    $table->unsignedInteger('number');
		    $table->string('text');
	    });

	    // Create poll votes table
	    Schema::create("poll_votes", function(Blueprint $table) {
		    $table->increments('id');
		    $table->unsignedInteger('option_id');
		    $table->foreign('option_id')->references('id')->on('poll_options')->onDelete('cascade');
		    $table->unsignedInteger('user_id');
		    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
	    Schema::drop('poll_votes');
	    Schema::drop('poll_options');
        Schema::drop('polls');
    }
}
