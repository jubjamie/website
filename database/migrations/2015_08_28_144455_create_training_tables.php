<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrainingTables extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('training_categories', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->timestamps();
		});

		Schema::create('training_skills', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->unsignedInteger('category_id')->nullable();
			$table->text('description');
			$table->text('requirements_level1');
			$table->text('requirements_level2');
			$table->text('requirements_level3');
			$table->timestamps();

			$table->foreign('category_id')->references('id')->on('training_categories')->onDelete('set null');
		});

		Schema::create('training_awarded_skills', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('skill_id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('level');
			$table->unsignedInteger('awarded_by')->nullable();
			$table->timestamps();

			$table->foreign('skill_id')->references('id')->on('training_skills')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('awarded_by')->references('id')->on('users')->onDelete('set null');
		});

		Schema::create('training_skill_proposals', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('skill_id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('proposed_level');
			$table->text('reasoning');
			$table->dateTime('date');
			$table->unsignedInteger('awarded_level')->nullable();
			$table->unsignedInteger('awarded_by')->nullable();
			$table->text('awarded_comment')->nullable();
			$table->dateTime('awarded_date')->nullable();

			$table->foreign('skill_id')->references('id')->on('training_skills')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('awarded_by')->references('id')->on('users')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::drop('training_skill_proposals');
		Schema::drop('training_awarded_skills');
		Schema::drop('training_skills');
		Schema::drop('training_categories');
	}
}
