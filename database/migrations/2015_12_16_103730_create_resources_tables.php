<?php

use App\Permission;
use App\ResourceCategory;
use App\ResourceTag;
use App\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CreateResourcesTables extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		// Categories
		Schema::create('resource_categories', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug');
			$table->unsignedInteger('flag')->nullable()->comment = '1 = RA, 2 = Event Report, 3 = Agenda, 4 = Minutes';
		});

		// Tags table
		Schema::create('resource_tags', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug');
		});

		// Resources table
		Schema::create('resources', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->text('description')->nullable();
			$table->unsignedInteger('category_id')->nullable();
			$table->unsignedInteger('event_id')->nullable();
			$table->unsignedInteger('author_id')->nullable();
			$table->unsignedInteger('type')->comment = "1 = file, 2 = gdoc";
			$table->text('href')->nullable();
			$table->unsignedInteger('access_id')->nullable();
			$table->timestamps();

			$table->foreign('category_id')
			      ->references('id')
			      ->on('resource_categories')
			      ->onDelete('set null');
			$table->foreign('event_id')
			      ->references('id')
			      ->on('events')
			      ->onDelete('set null');
			$table->foreign('author_id')
			      ->references('id')
			      ->on('users')
			      ->onDelete('set null');
			$table->foreign('access_id')
			      ->references('id')
			      ->on('permissions')
			      ->onDelete('set null');

		});
		DB::statement('ALTER TABLE resources ADD FULLTEXT search(title, description)');

		// Tags pivot table
		Schema::create('resource_tag', function (Blueprint $table) {
			$table->unsignedInteger('resource_id');
			$table->unsignedInteger('resource_tag_id');

			$table->foreign('resource_id')
			      ->references('id')
			      ->on('resources')
			      ->onDelete('cascade');
			$table->foreign('resource_tag_id')
			      ->references('id')
			      ->on('resource_tags')
			      ->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::table('resources', function ($table) {
			$table->dropIndex('search');
		});
		DB::table('permissions')->where('name', 'LIKE', 'resources.%')->delete();
		Schema::drop('resource_tag');
		Schema::drop('resources');
		Schema::drop('resource_tags');
		Schema::drop('resource_categories');
		File::cleanDirectory(base_path('resources/resources'));
	}
}
