<?php
/*
|--------------------------------------------------------------------------
| Event routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the event functions.
|
*/

Route::group([
	'prefix' => 'events',
], function () {
	// Index
	Route::get('', [
		'as'   => 'events.index',
		'uses' => 'EventsController@index',
	]);
	// Diary
	Route::get('diary/{year?}/{month?}', [
		'as'   => 'events.diary',
		'uses' => 'EventsController@diary',
	])->where('year', '[\d]{4}')->where('month', '[\d]{1,2}');
	// View
	Route::group([
		'prefix' => '{id}',
		'where'  => ['id' => '[\d]+'],
	], function () {
		Route::get('', [
			'as'   => 'events.view',
			'uses' => 'EventsController@view',
		]);
		Route::post('volunteer', [
			'as'   => 'events.volunteer',
			'uses' => 'EventsController@toggleVolunteer',
		]);
		Route::post('delete', [
			'as'   => 'events.delete',
			'uses' => 'EventsController@destroy',
		]);
		Route::post('email', [
			'as'   => 'events.email',
			'uses' => 'EventsController@emailCrew',
		]);
		Route::post('{action}', [
			'as'   => 'events.update',
			'uses' => 'EventsController@update',
		]);
		Route::get('finance-email', [
			'uses' => 'EventsController@sendFinanceEmail',
		]);
	});
	// Add
	Route::get('add', [
		'as'   => 'events.add',
		'uses' => 'EventsController@create',
	]);
	Route::post('add', [
		'as'   => 'events.add.do',
		'uses' => 'EventsController@store',
	]);
	// Signup
	Route::get('signup/{tab?}', [
		'as'   => 'events.signup',
		'uses' => 'EventsController@signup',
	])->where('tab', 'em|crew');
	// Member diary
	Route::get('diary/{username}/{year?}/{month?}', [
		'as'   => 'events.memberdiary',
		'uses' => 'EventsController@memberDiary',
	])->where('username', '[\w]+')->where('year', '[\d]{4}')->where('month', '[\d]{1,2}');
	// My diary
	Route::get('my-diary/{year?}/{month?}', [
		'as'   => 'events.mydiary',
		'uses' => 'EventsController@myDiary',
	])->where('year', '[\d]{4}')->where('month', '[\d]{1,2}');
	// Export
	Route::get('export', [
		'as'   => 'events.export',
		'uses' => 'EventsController@export',
	]);
});