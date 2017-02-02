<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class FixTimestampSetup extends Migration
    {
        /**
         * Run the migrations.
         * @return void
         */
        public function up()
        {
            // Set up the tables and columns to correct
            $tables  = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $columns = [];
            foreach($tables as $table_name) {
                $columns[$table_name] = [
                    'created_at',
                    'updated_at',
                ];
            }
            
            // Other columns to correct
            $columns['elections'][]                = 'nominations_start';
            $columns['elections'][]                = 'nominations_end';
            $columns['elections'][]                = 'voting_start';
            $columns['elections'][]                = 'voting_end';
            $columns['elections'][]                = 'hustings_time';
            $columns['event_times'][]              = 'start';
            $columns['event_times'][]              = 'end';
            $columns['quotes'][]                   = 'date';
            $columns['training_skill_proposals'][] = 'date';
            $columns['training_skill_proposals'][] = 'awarded_date';
            
            foreach($columns as $table_name => $column_names) {
                foreach($column_names as $column) {
                    Schema::table($table_name, function (Blueprint $table) use ($table_name, $column) {
                        if(Schema::hasColumn($table_name, $column)) {
                            DB::table($table_name)
                              ->where($column, '0000-00-00 00:00:00')
                              ->update([
                                  $column => '1970-01-01 00:00:01',
                              ]);
                            $table->dateTime($column)
                                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                                  ->change();
                        }
                    });
                }
            }
        }
        
        /**
         * Reverse the migrations.
         * @return void
         */
        public function down()
        {
            
        }
    }
