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

// Home page
Route::get('/', [
	'as' => 'home',
	function () {
		return App::make('App\Http\Controllers\PagesController')->show('home');
	},
]);

// Committee
Route::group([
	'prefix' => 'committee',
], function () {
	// View
	Route::get('', [
		'as'   => 'committee.view',
		'uses' => 'CommitteeController@view',
	]);
	// Add
	Route::post('add', [
		'as'         => 'committee.add',
		'middleware' => 'auth.permission:admin',
		'uses'       => 'CommitteeController@store',
	]);
	// Edit
	Route::post('edit', [
		'as'         => 'committee.edit',
		'middleware' => 'auth.permission:admin',
		'uses'       => 'CommitteeController@update',
	]);
});

// Contact forms
Route::group([
	'prefix' => 'contact',
], function () {
	// Enquiries
	Route::get('enquiries', [
		'as'   => 'contact.enquiries',
		'uses' => 'ContactController@getEnquiries',
	]);
	Route::post('enquiries', [
		'as'   => 'contact.enquiries.do',
		'uses' => 'ContactController@postEnquiries',
	]);
	// Book
	Route::get('book', [
		'as'   => 'contact.book',
		'uses' => 'ContactController@getBook',
	]);
	Route::post('book', [
		'as'   => 'contact.book.do',
		'uses' => 'ContactController@postBook',
	]);
	// Book T&Cs
	Route::get('book/terms', [
		"as"   => "contact.book.terms",
		"uses" => "ContactController@getBookTerms",
	]);
	// Feedback
	Route::get('feedback', [
		'as'   => 'contact.feedback',
		'uses' => 'ContactController@getFeedback',
	]);
	Route::post('feedback', [
		'as'   => 'contact.feedback.do',
		'uses' => 'ContactController@postFeedback',
	]);
	// Report accident
	Route::get('accident', [
		'as'         => 'contact.accident',
		'middleware' => 'auth',
		'uses'       => 'ContactController@getAccident',
	]);
	Route::post('accident', [
		'as'         => 'contact.accident.do',
		'middleware' => 'auth',
		'uses'       => 'ContactController@postAccident',
	]);
});

// /members
Route::group([
	'middleware' => 'auth',
	'prefix'     => 'members',
], function () {
	Route::get('{dash?}', [
		'as'   => 'members.dash',
		'uses' => function () {
			return redirect(route('home'));
		},
	])->where('dash', 'dash');
});

// Pages
Route::group([
	'prefix' => 'page',
], function () {
	// List
	Route::get('index', [
		'as'   => 'page.index',
		'uses' => 'PagesController@index',
	]);
	// Create
	Route::get('create', [
		'as'   => 'page.create',
		'uses' => 'PagesController@create',
	]);
	Route::post('', [
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

// Polls
Route::group([
	'prefix'     => 'polls',
	'middleware' => 'auth.permission:member',
], function () {
	Route::get('', [
		'as'   => 'polls.index',
		'uses' => 'PollsController@index',
	]);
	Route::group([
		'prefix'     => 'create',
		'middleware' => 'auth.permission:admin',
	], function () {
		Route::get('', [
			'as'   => 'polls.create',
			'uses' => 'PollsController@create',
		]);
		Route::post('', [
			'as'         => 'polls.store',
			'middleware' => 'auth.permission:admin',
			'uses'       => 'PollsController@store',
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
			'as'         => 'polls.delete',
			'middleware' => 'auth.permission:admin',
			'uses'       => 'PollsController@delete',
		]);
	});
});

// Quotesboard
Route::group([
	"prefix"     => "quotesboard",
	"middleware" => "auth.permission:member",
], function () {
	Route::get('', [
		'as'   => 'quotes.index',
		'uses' => 'QuotesController@index',
	]);
	Route::post('add', [
		'as'   => 'quotes.add',
		'uses' => 'QuotesController@store',
	]);
	Route::post('delete', [
		'as'         => 'quotes.delete',
		'middleware' => 'auth.permission:admin',
		'uses'       => 'QuotesController@destroy',
	]);
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

// Teapot :p
Route::get('im/a/teapot', function () {
	App::abort(418);
});