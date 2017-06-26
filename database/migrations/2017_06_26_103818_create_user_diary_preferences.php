<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserDiaryPreferences extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('diary_preferences');
        });
        
        DB::update('update users set diary_preferences = ?', [
            json_encode([
                'event_types' => ['event', 'training', 'social', 'meeting', 'hidden'],
                'crewing'      => '*',
            ]),
        ]);
    }
    
    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('diary_preferences');
        });
    }
}
