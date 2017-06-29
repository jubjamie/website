<?php

namespace App\Console;

use App\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
        //
    ];
    
    /**
     * Define the application's command schedule.
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->closePastEventCrewLists($schedule);
    }
    
    /**
     * Register the Closure based commands for the application.
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
    
    /**
     * Close the crew list for past events.
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    private function closePastEventCrewLists(Schedule $schedule)
    {
        $schedule->call(function () {
            $events = Event::past()
                           ->where('events.crew_list_status', 1)
                           ->get();
            
            foreach($events as $event) {
                $event->update([
                    'crew_list_status' => 0,
                ]);
            }
        })->daily();
    }
}
