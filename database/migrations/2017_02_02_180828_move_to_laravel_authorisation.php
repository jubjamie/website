<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveToLaravelAuthorisation extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        // Create user groups table
        Schema::create('user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('name');
        });
        
        // Create index for storing user group
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('user_group_id')
                  ->nullable();
            $table->foreign('user_group_id')
                  ->references('id')
                  ->on('user_groups')
                  ->onDelete('set null');
        });
        
        // Create groups
        DB::table('user_groups')->insert([
            'name'  => 'member',
            'title' => 'Member',
        ]);
        DB::table('user_groups')->insert([
            'name'  => 'committee',
            'title' => 'Committee Member',
        ]);
        DB::table('user_groups')->insert([
            'name'  => 'associate',
            'title' => 'Associate',
        ]);
        DB::table('user_groups')->insert([
            'name'  => 'staff',
            'title' => 'SU / University Staff',
        ]);
        DB::table('user_groups')->insert([
            'name'  => 'super_admin',
            'title' => 'Super Admin',
        ]);
        
        // Set all user groups
        $users = DB::table('users')->get();
        foreach($users as $user) {
            $roles = DB::table('role_user')
                       ->where('user_id', $user->id)
                       ->orderBy('role_user.role_id', 'desc')
                       ->first();
            
            DB::table('users')
              ->where('id', $user->id)
              ->update([
                  'user_group_id' => $roles ? $roles->role_id : 1,
              ]);
        }
        
        // Drop the foreign key on resources
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['access_id']);
        });
        
        // Drop the old authorisation tables
        Schema::disableForeignKeyConstraints();
        Schema::drop('permission_role');
        Schema::drop('role_user');
        Schema::drop('roles');
        Schema::drop('permissions');
        Schema::enableForeignKeyConstraints();
    }
    
    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_group_id']);
            $table->dropColumn('user_group_id');
        });
        Schema::drop('user_groups');
    }
}
