<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRepairsDbTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('equipment_breakages', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('location');
			$table->string('label');
			$table->text('description');
			$table->text('comment');
			$table->unsignedInteger('status');
			$table->boolean('closed');
			$table->unsignedInteger('user_id')->nullable();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::drop('equipment_breakages');
	}
}
