<?php
/*
|--------------------------------------------------------------------------
| Poll routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the member poll functions.
|
*/

Route::group([
	'prefix' => 'polls',
], function () {
	Route::get('', [
		'as'   => 'polls.index',
		'uses' => 'PollsController@index',
	]);
	Route::group([
		'prefix' => 'create',
	], function () {
		Route::get('', [
			'as'   => 'polls.create',
			'uses' => 'PollsController@create',
		]);
		Route::post('', [
			'as'   => 'polls.store',
			'uses' => 'PollsController@store',
		]);
		Route::post('addOption', [
			'as'   => 'polls.store.addOption',
			'uses' => 'PollsController@addOption',
		]);
		Route::post('delOption', [
			'as'   => 'polls.store.delOption',
			'uses' => 'PollsController@deleteOption',
		]);
	});
	Route::group([
		'prefix' => '{id}',
		'where'  => ['id' => '[\d]+'],
	], function () {
		Route::get('', [
			'as'   => 'polls.view',
			'uses' => 'PollsController@show',
		]);
		Route::post('vote', [
			'as'   => 'polls.vote',
			'uses' => 'PollsController@castVote',
		]);
		Route::get('delete', [
			'as'   => 'polls.delete',
			'uses' => 'PollsController@delete',
		]);
	});
});