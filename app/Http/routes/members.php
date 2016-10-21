<?php
/*
|--------------------------------------------------------------------------
| Member routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the member functions.
|
*/

Route::group([
	'middleware' => 'auth',
	'prefix'     => 'members',
], function () {
	// Dashboard
	Route::get('', [
		'as'   => 'members.index',
		'uses' => function () {
			return redirect(route('members.dash'));
		},
	]);
	Route::get('dash', [
		'as'   => 'members.dash',
		'uses' => 'MembersController@dash',
	]);
	// Profile
	Route::get('profile/{username}/{tab?}', [
		'as'   => 'members.profile',
		'uses' => 'MembersController@profile',
	])->where('username', '[\w\.]+')
	     ->where('tab', 'profile|events|training');
	// My profile
	Route::get('my-profile/{tab?}', [
		'as'   => 'members.myprofile',
		'uses' => 'MembersController@getMyProfile',
	])->where('tab', 'profile|events|training');
	Route::post('my-profile', [
		'as'   => 'members.myprofile.do',
		'uses' => 'MembersController@postMyProfile',
	]);
	Route::post('my-profile/password', [
		'as'   => 'members.myprofile.password',
		'uses' => 'MembersController@updatePassword',
	]);
});

Route::get('membership', [
	'as'   => 'membership',
	'uses' => 'MembersController@membership',
]);
Route::get('membership/{modifier}/{term}', [
	'uses' => 'MembersController@membership',
])->where('modifier', 'search');