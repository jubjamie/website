<?php
/*
|--------------------------------------------------------------------------
| Committee routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the quotesboard.
|
*/

Route::group([
	"prefix" => "quotesboard",
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
		'as'   => 'quotes.delete',
		'uses' => 'QuotesController@destroy',
	]);
});