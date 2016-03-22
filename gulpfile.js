var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.config.sourcemaps = false;
elixir(function (mix) {
	mix
	// Copy vendor files
		.copy('vendor/components/jquery/jquery.min.js', 'resources/assets/js/vendors/jquery.js')
		.copy('vendor/twbs/bootstrap/dist/js/bootstrap.min.js', 'resources/assets/js/vendors/bootstrap.js')
		.copy('vendor/twbs/bootstrap/dist/css/bootstrap.min.css', 'resources/assets/css/vendors/bootstrap.css')
		.copy('vendor/twbs/bootstrap/dist/css/bootstrap-theme.min.css', 'resources/assets/css/vendors/bootstrap-theme.css')
		.copy('vendor/moment/moment/min/moment.min.js', 'resources/assets/js/vendors/moment.js')
		.copy('vendor/select2/select2/dist/js/select2.min.js', 'resources/assets/js/vendors/select2.js')
		.copy('vendor/select2/select2/dist/css/select2.min.css', 'resources/assets/css/vendors/select2.css')
		.copy('vendor/eonasdan/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js', 'resources/assets/js/vendors/datetimepicker.js')
		.copy('vendor/eonasdan/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css', 'resources/assets/css/vendors/datetimepicker.css')

		// Combine vendors files
		.sass('vendors/font-awesome/font-awesome.scss', 'resources/assets/css/vendors/font-awesome.css')
		.scripts([
			'vendors/jquery.js',
			'vendors/moment.js',
			'vendors/bootstrap.js',
			'vendors/select2.js',
			'vendors/datetimepicker.js'
		], 'public/js/vendors.js')
		.styles([
			'vendors/reset.css',
			'vendors/bootstrap.css',
			'vendors/bootstrap-theme.css',
			'vendors/select2.css',
			'vendors/select2-bootstrap.css',
			'vendors/font-awesome.css',
			'vendors/datetimepicker.css'
		], 'public/css/vendors.css')

		// Create app files
		.sass('app.scss', 'public/css/app.css')
		.sass('tinymce.scss', 'public/css/tinymce.css')
		.scripts([
			'plugins/tabify.js',
			'plugins/CloseMessages.js',
			'plugins/DisableButtons.js',
			'plugins/modal.js',
			'plugins/EditableFields.js',
			'app.js',
		], 'public/js/app.js')
	//.copy('resources/assets/js/tinymce', 'public/js/tinymce')
});
