const {mix} = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

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
	// .copy('vendor/fortawesome/font-awesome/scss', 'resources/assets/sass/vendors/font-awesome')
	// .sass('resources/assets/sass/vendors/font-awesome/font-awesome.scss', '../resources/assets/css/vendors/font-awesome.css')
		
	// Combine the vendor files
	.combine([
		'resources/assets/js/vendors/jquery.js',
		'resources/assets/js/vendors/moment.js',
		'resources/assets/js/vendors/bootstrap.js',
		'resources/assets/js/vendors/bootstrap-markdown.js',
		'resources/assets/js/vendors/select2.js',
		'resources/assets/js/vendors/datetimepicker.js',
		'node_modules/simplemde/dist/simplemde.min.js',
		'node_modules/js-cookie/src/js.cookie.js'
	], 'public/js/vendors.js')
	.combine([
		'resources/assets/css/reset.css',
		'resources/assets/css/vendors/bootstrap.css',
		'resources/assets/css/vendors/bootstrap-theme.css',
		'node_modules/simplemde/dist/simplemde.min.css',
		'resources/assets/css/vendors/select2.css',
		// 'resources/assets/css/vendors/select2-bootstrap.css',
		'resources/assets/css/vendors/font-awesome.css',
		'resources/assets/css/vendors/datetimepicker.css'
	], 'public/css/vendors.css')
	
	// Process SCSS files
	.sass('resources/assets/sass/structure/current.scss', 'public/css/structure.css')
	.sass('resources/assets/sass/general/general.scss', 'public/css/general.css')
	.sass('resources/assets/sass/partials/partials.scss', 'public/css/content.css')

	// Process JS files
	.combine([
		'resources/assets/js/plugins/CloseMessages.js',
		'resources/assets/js/plugins/CookieAcceptance.js',
		'resources/assets/js/plugins/DisableButtons.js',
		'resources/assets/js/plugins/SimpleMDE.js',
		'resources/assets/js/plugins/modal.js',
		'resources/assets/js/plugins/tabify.js',
		'resources/assets/js/app.js'
	], 'public/js/app.js');