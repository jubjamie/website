<?php
/*
|--------------------------------------------------------------------------
| Page routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the webpage functions.
|
*/

Route::group([
	'prefix' => 'page',
], function () {
	// List
	Route::get('', [
		'as'   => 'page.index',
		'uses' => 'PagesController@index',
	]);
	// Create
	Route::get('create', [
		'as'   => 'page.create',
		'uses' => 'PagesController@create',
	]);
	Route::post('create', [
		'as'   => 'page.store',
		'uses' => 'PagesController@store',
	]);
	Route::group([
		'prefix' => '{slug}',
		'where'  => ['slug' => '[\w-]+'],
	], function () {
		// View
		Route::get('', [
			'as'   => 'page.show',
			'uses' => 'PagesController@show',
		]);
		// Delete
		Route::get('delete', [
			'as'   => 'page.destroy',
			'uses' => 'PagesController@destroy',
		]);
		// Edit
		Route::get('edit', [
			'as'   => 'page.edit',
			'uses' => 'PagesController@edit',
		]);
		Route::post('edit', [
			'as'   => 'page.update',
			'uses' => 'PagesController@update',
		]);
	});
});