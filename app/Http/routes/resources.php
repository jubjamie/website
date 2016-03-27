<?php
/*
|--------------------------------------------------------------------------
| Resources routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the resources area.
|
*/

Route::group([
	'prefix' => 'resources',
], function () {
	// Search
	Route::get('', [
		'as'   => 'resources.index',
		'uses' => 'ResourceController@index',
	]);
	Route::get('{modifier}/{term}', [
		'uses' => 'ResourceController@index',
	])->where('modifier', 'filter');
	Route::group([
		'prefix' => 'search',
	], function () {
		Route::get('', [
			'as'   => 'resources.search',
			'uses' => 'ResourceController@searchHandle',
		]);
		Route::post('', [
			'as'   => 'resources.search.do',
			'uses' => 'ResourceController@searchProcess',
		]);
	});
	// Create
	Route::group([
		'prefix' => 'create',
	], function () {
		Route::get('', [
			'as'   => 'resources.create',
			'uses' => 'ResourceController@create',
		]);
		Route::post('', [
			'as'   => 'resources.store',
			'uses' => 'ResourceController@store',
		]);
	});
	// Single resource actions
	Route::group([
		'prefix' => '{id}',
		'where'  => ['id' => '[\d]+'],
	], function () {
		// View
		Route::get('', [
			'as'   => 'resources.view',
			'uses' => 'ResourceController@view',
		]);
		Route::get('view', [
			'as'   => 'resources.stream',
			'uses' => 'ResourceController@stream',
		]);
		// Download
		Route::get('download', [
			'as'   => 'resources.download',
			'uses' => 'ResourceController@download',
		]);
		// Edit
		Route::get('edit', [
			'as'   => 'resources.edit',
			'uses' => 'ResourceController@edit',
		]);
		Route::post('edit', [
			'as'   => 'resources.update',
			'uses' => 'ResourceController@update',
		]);
		// Delete
		Route::post('delete', [
			'as'   => 'resources.delete',
			'uses' => 'ResourceController@destroy',
		]);
	});
	// Categories
	Route::group([
		'prefix' => 'categories',
	], function () {
		// List
		Route::get('', [
			'as'   => 'resources.category.list',
			'uses' => 'ResourceCategoryController@index',
		]);
		// Add
		Route::post('create', [
			'as'   => 'resources.category.create',
			'uses' => 'ResourceCategoryController@store',
		]);
		// Update
		Route::post('{id}/update', [
			'as'   => 'resources.category.update',
			'uses' => 'ResourceCategoryController@update',
		])->where('id', '[\d]+');
		// Delete
		Route::post('{id}/delete', [
			'as'   => 'resources.category.delete',
			'uses' => 'ResourceCategoryController@destroy',
		])->where('id', '[\d]+');
	});
	// Tags
	Route::group([
		'prefix' => 'tags',
	], function () {
		// List
		Route::get('', [
			'as'   => 'resources.tag.list',
			'uses' => 'ResourceTagController@index',
		]);
		// Add
		Route::post('create', [
			'as'   => 'resources.tag.create',
			'uses' => 'ResourceTagController@store',
		]);
		// Update
		Route::post('{id}/update', [
			'as'   => 'resources.tag.update',
			'uses' => 'ResourceTagController@update',
		])->where('id', '[\d]+');
		// Delete
		Route::post('{id}/delete', [
			'as'   => 'resources.tag.delete',
			'uses' => 'ResourceTagController@destroy',
		])->where('id', '[\d]+');
	});
});