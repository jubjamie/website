<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 * @return void
	 */
	public function boot()
	{
		Blade::directive('helpDoc', function($expression) {
			return "<?php include base_path('resources/help_docs/' . str_replace('.', '/', {$expression}) . '.html'); ?>";
		});
	}

	/**
	 * Register any application services.
	 * @return void
	 */
	public function register()
	{
		//
	}
}
