<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElectionTables extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('elections', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('type');
			$table->text('positions');
			$table->dateTime('nominations_start');
			$table->dateTime('nominations_end');
			$table->dateTime('voting_start');
			$table->dateTime('voting_end');
			$table->dateTime('hustings_time')->nullable();
			$table->string('hustings_location')->nullable();
			$table->timestamps();
		});

		Schema::create('election_nominations', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('election_id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('position');
			$table->boolean('elected');

			$table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::drop('election_nominations');
		Schema::drop('elections');
	}
}
