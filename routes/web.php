<?php
    
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
    
    Route::get('/', [
        'as'   => 'home',
        'uses' => function () {
            return view('home');
        },
    ]);
    Route::get('/random', function () {
        return view('random');
    });
    
    include base_path('routes/web/auth.php');
    include base_path('routes/web/committee.php');
    include base_path('routes/web/quotes.php');
    include base_path('routes/web/page.php');