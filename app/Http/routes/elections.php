<?php
/*
|--------------------------------------------------------------------------
| Election routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the election functions.
|
*/

Route::group([
	'prefix' => 'elections',
], function () {
	// Index
	Route::get('', [
		'as'   => 'elections.index',
		'uses' => 'ElectionController@index',
	]);
	// Create
	Route::get('create', [
		'as'   => 'elections.create',
		'uses' => 'ElectionController@create',
	]);
	Route::post('create', [
		'as'   => 'elections.create.do',
		'uses' => 'ElectionController@store',
	]);
	// Single election routes
	Route::group([
		'prefix' => '{id}',
		'where'  => ['id' => '[\d]+'],
	], function () {
		// View
		Route::get('', [
			'as'   => 'elections.view',
			'uses' => 'ElectionController@view',
		]);
		// Edit
		Route::get('edit', [
			'as'   => 'elections.edit',
			'uses' => 'ElectionController@edit',
		]);
		Route::post('edit', [
			'as'   => 'elections.update',
			'uses' => 'ElectionController@update',
		]);
		// Delete
		Route::post('delete', [
			'as'   => 'elections.delete',
			'uses' => 'ElectionController@destroy',
		]);
		// Nominate
		Route::post('nominate', [
			'as'   => 'elections.nominate',
			'uses' => 'ElectionController@addNominee',
		]);
		// Remove nomination
		Route::post('nomination/{nomineeId}/delete', [
			'as'   => 'elections.nomination.delete',
			'uses' => 'ElectionController@removeNominee',
		])->where('nomineeId', '[\d]+');
		// View manifesto
		Route::get('nomination/{nomineeId}/manifesto', [
			'as'   => 'elections.manifesto',
			'uses' => 'ElectionController@manifesto',
		]);
		// // Elect
		Route::post('elect', [
			'as'   => 'elections.elect',
			'uses' => 'ElectionController@elect',
		]);
	});
});