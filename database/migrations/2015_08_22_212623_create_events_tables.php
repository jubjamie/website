<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTables extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('venue');
			$table->unsignedInteger('em_id')->nullable();
			$table->text('description');
			$table->text('description_public');
			$table->unsignedInteger('type')->comment = "1 = event, 2 = training, 3 = social, 4 = meeting, 5 = hidden";
			$table->smallInteger('crew_list_status')->comment = '-1 = hidden, 0 = closed, 1 = open';
			$table->unsignedInteger('client_type')->comment = '1 = su, 2 = uni, 3 = external';
			$table->unsignedInteger('venue_type')->comment = '1 = on-campus, 2 = off-campus';
			$table->text('paperwork');
			$table->timestamps();

			$table->foreign('em_id')->references('id')->on('users')->onDelete('set null');
		});

		Schema::create('event_times', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('event_id');
			$table->string('name');
			$table->dateTime('start');
			$table->dateTime('end');
			$table->timestamps();

			$table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
		});

		Schema::create('event_crew', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('event_id');
			$table->unsignedInteger('user_id');
			$table->string('name')->nullable()->comment = "NULL = not core crew";
			$table->boolean('em');
			$table->boolean('confirmed');

			$table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::create('event_emails', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('event_id');
			$table->unsignedInteger('sender_id')->nullable();
			$table->string('header');
			$table->text('body');
			$table->timestamps();

			$table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
			$table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::drop('event_emails');
		Schema::drop('event_crew');
		Schema::drop('event_times');
		Schema::drop('events');
	}
}
