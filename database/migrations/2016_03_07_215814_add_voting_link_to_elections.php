<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotingLinkToElections extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::table('elections', function (Blueprint $table) {
			$table->unsignedInteger('bathstudent_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::table('elections', function (Blueprint $table) {
			$table->dropColumn('bathstudent_id');
		});
	}
}
