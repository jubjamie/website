<?php
/*
|--------------------------------------------------------------------------
| Gallery routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the photo gallery.
|
*/

Route::group([
	'prefix' => 'gallery',
], function () {
	// Index
	Route::get('', [
		'as'   => 'gallery.index',
		'uses' => 'GalleryController@index',
	]);
	// Album
	Route::get('album/{id}', [
		'as'   => 'gallery.album',
		'uses' => 'GalleryController@show',
	]);
});