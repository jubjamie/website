<?php
    Route::group([
        'prefix' => 'media',
    ], function () {
        // Index
        Route::get('images', [
            'as'   => 'media.images.index',
            'uses' => 'Media\ImageController@index',
        ]);
        // Album
        Route::get('images/album/{id}', [
            'as'   => 'media.images.album',
            'uses' => 'Media\ImageController@album',
        ]);
    });