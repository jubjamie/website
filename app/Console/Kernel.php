<?php

namespace App\Console;

use App\Console\Commands\Inspire;
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
		Inspire::class,
	];

	/**
	 * Define the application's command schedule.
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// Automatically close crew lists
		$schedule->call(function () {
			$events = Event::select('events.*')
			               ->leftJoin('event_times', 'events.id', '=', 'event_times.event_id')
			               ->where('events.crew_list_status', 1)
			               ->where('event_times.end', '<', date('Y-m-d H:i:s', mktime(0, 0, 0)))
			               ->distinct()
			               ->get();

			foreach($events as $event) {
				$event->update([
					'crew_list_status' => 0,
				]);
			}
		})->daily();
	}
}
