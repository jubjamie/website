<?php
Route::group([
    'prefix' => 'equipment',
], function () {
    // Asset Register
    Route::get('assets', [
        'as'   => 'equipment.assets',
        'uses' => 'Equipment\AssetController@view',
    ]);
});