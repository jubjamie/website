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
