<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowGuestCrew extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::table('event_crew', function (Blueprint $table) {
			$table->unsignedInteger('user_id')->nullable()->change();
			$table->string('guest_name', 100);
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::table('event_crew', function (Blueprint $table) {
			$table->unsignedInteger('user_id')->change();
			$table->dropColumn('guest_name');
		});
	}
}
