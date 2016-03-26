<?php
/*
|--------------------------------------------------------------------------
| User routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the user functions.
|
*/

Route::group([
	'prefix' => 'users',
], function () {
	// List
	Route::get('', [
		'as'   => 'user.index',
		'uses' => 'UsersController@index',
	]);
	Route::get('{modifier?}/{term?}', [
		'uses' => 'UsersController@index',
	])->where('modifier', 'filter|search');
	Route::post('', [
		'as'   => 'user.index.do',
		'uses' => 'UsersController@bulkUpdate',
	]);
	// Create
	Route::get('create', [
		'as'   => 'user.create',
		'uses' => 'UsersController@create',
	]);
	Route::post('create', [
		'as'   => 'user.create.do',
		'uses' => 'UsersController@store',
	]);
	// View
	Route::get('{username}', [
		'as'   => 'user.view',
		'uses' => function ($username) {
			return redirect(route('members.profile', $username));
		},
	])->where('username', '[\w]+');
	// Edit
	Route::get('{username}/edit', [
		'as'   => 'user.edit',
		'uses' => 'UsersController@edit',
	])->where('username', '[\w]+');
	Route::post('{username}/edit', [
		'as'   => 'user.edit.do',
		'uses' => 'UsersController@update',
	])->where('username', '[\w]+');
});

// Authentication
Route::get('login', [
	'as'   => 'auth.login',
	'uses' => 'AuthController@getLogin',
]);
Route::post('login', [
	'as'   => 'auth.login.do',
	'uses' => 'AuthController@postLogin',
]);
Route::get('logout', [
	'as'   => 'auth.logout',
	'uses' => 'AuthController@getLogout',
]);
Route::group([
	'prefix' => 'password',
], function () {
	Route::get('email', [
		'as'   => 'pwd.email',
		'uses' => 'AuthController@getEmail',
	]);
	Route::post('email', [
		'as'   => 'pwd.email.do',
		'uses' => 'AuthController@postEmail',
	]);
	Route::get('reset/{token}', [
		'as'   => 'pwd.reset',
		'uses' => 'AuthController@getReset',
	]);
	Route::post('reset/{token}', [
		'as'   => 'pwd.reset.do',
		'uses' => 'AuthController@postReset',
	]);
});