<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceIssueSystem extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        // Create the issue table
        Schema::create('resource_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('issue');
            $table->unsignedInteger('resource_id');
            $table->unsignedInteger('author_id')->nullable();
            $table->text('reason');
            $table->timestamps();
            
            $table->foreign('author_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
        
        // Set up the initial issue for all resources
        $resources = DB::table('resources')->get();
        foreach($resources as $resource) {
            DB::table('resource_issues')
              ->insert([
                  'issue'       => 1,
                  'author_id'   => $resource->author_id,
                  'resource_id' => $resource->id,
                  'reason'      => 'Initial issue',
                  'updated_at'  => $resource->created_at,
                  'created_at'  => $resource->created_at,
              ]);
            
            mkdir(resource_path('resources/' . $resource->id));
            rename(resource_path('resources/' . $resource->id . '.pdf'),
                resource_path('resources/' . $resource->id . '/iss01.pdf'));
        }
        
        // Set the foreign key
        Schema::table('resource_issues', function (Blueprint $table) {
            $table->foreign('resource_id')
                  ->references('id')
                  ->on('resources')
                  ->onDelete('cascade');
        });
        // Drop the resource author_id
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
        });
    }
    
    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        // Drop the foreign key
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['issue_id']);
            $table->dropColumn('issue_id');
            
            $table->unsignedInteger('author_id')->nullable();
            $table->foreign('author_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
        
        // Drop the issue table
        Schema::drop('resource_issues');
    }
}
