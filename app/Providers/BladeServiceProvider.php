<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 * @return void
	 */
	public function boot()
	{
		Blade::directive('HelpDoc', function ($expression) {
			$path = $this->sanitizeExpression($expression);

			return "<?php echo \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml(file_get_contents(base_path('resources/help_docs/' . str_replace('.', '/', {$path}) . '.md'))); ?>";
		});
		Blade::directive('InputClass', function ($name) {
			$name = $this->sanitizeExpression($name);

			return "<?php echo \$errors->any() ? (\$errors->default->has({$name}) ? 'has-error' : 'has-success') : ''; ?>";
		});
		Blade::directive('InputError', function ($name) {
			$name = $this->sanitizeExpression($name);

			return "<?php echo \$errors->any() && \$errors->default->has({$name}) ? ('<p class=\"help-block\">' . \$errors->default->first({$name}) . '</p>') : ''; ?>";
		});
	}

	/**
	 * Register the application services.
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Sanitise the expression to allow it to be passed to PHP nicely.
	 * @param $param
	 * @return mixed
	 */
	private function sanitizeExpression($param)
	{
		preg_match('/^\((.*)\)$/', $param, $matches);

		return $matches[1];
	}
}
