<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Grouped routes
|--------------------------------------------------------------------------
*/
require app_path('Http/routes/committee.php');
require app_path('Http/routes/contact.php');
require app_path('Http/routes/elections.php');
require app_path('Http/routes/equipment.php');
require app_path('Http/routes/events.php');
require app_path('Http/routes/gallery.php');
require app_path('Http/routes/members.php');
require app_path('Http/routes/pages.php');
require app_path('Http/routes/polls.php');
require app_path('Http/routes/quotesboard.php');
require app_path('Http/routes/resources.php');
require app_path('Http/routes/training.php');
require app_path('Http/routes/users.php');

/*
|--------------------------------------------------------------------------
| Miscellaneous routes
|--------------------------------------------------------------------------
*/
// Home page
Route::get('/', [
	'as' => 'home',
	function () {
		return App::make('App\Http\Controllers\PagesController')->show('home');
	},
]);
// SU dashboard
Route::get('su-dash', [
	'as'   => 'su.dash',
	'uses' => 'MembersController@dashSU',
]);
// Easter eggs
Route::get('im/a/teapot', function () {
	App::abort(418);
});
Route::get('feeling-happy', [
	'middleware' => 'auth.permission:member',
	'uses'       => function () {
		return View::make('eggs.awesome')->with([
			'noNav'      => true,
			'slimFooter' => true,
		]);
	},
]);