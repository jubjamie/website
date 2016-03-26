<?php
/*
|--------------------------------------------------------------------------
| Contact routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the contact forms.
|
*/

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
		'as'   => 'contact.accident',
		'uses' => 'ContactController@getAccident',
	]);
	Route::post('accident', [
		'as'   => 'contact.accident.do',
		'uses' => 'ContactController@postAccident',
	]);
});