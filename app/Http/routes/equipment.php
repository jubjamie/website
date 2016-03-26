<?php
/*
|--------------------------------------------------------------------------
| Equipment routes
|--------------------------------------------------------------------------
|
| This file registers all of the routes for the equipment functions.
|
*/

Route::group([
	'prefix' => 'equipment',
], function () {
	// Dashboard
	Route::get('', [
		'as'   => 'equipment.dash',
		'uses' => 'EquipmentController@dash',
	]);
	// Asset register
	Route::get('asset', [
		'as'   => 'equipment.assets',
		'uses' => 'EquipmentController@assetRegister',
	]);
	// Repairs DB
	Route::group([
		'prefix' => 'repairs',
	], function () {
		// List
		Route::get('', [
			'as'   => 'equipment.repairs',
			'uses' => 'EquipmentController@repairsDb',
		]);
		// Add
		Route::get('add', [
			'as'   => 'equipment.repairs.add',
			'uses' => 'EquipmentController@getAddRepair',
		]);
		Route::post('add', [
			'as'   => 'equipment.repairs.add.do',
			'uses' => 'EquipmentController@postAddRepair',
		]);
		// View/edit
		Route::get('{id}', [
			'as'   => 'equipment.repairs.view',
			'uses' => 'EquipmentController@view',
		])->where('id', '[\d]+');
		Route::post('{id}', [
			'as'   => 'equipment.repairs.view.do',
			'uses' => 'EquipmentController@update',
		])->where('id', '[\d]+');
	});
});