<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Module routes are loaded by their respective service providers:
| - Auth: App\Modules\Auth\AuthServiceProvider
| - Chat: App\Modules\Chat\ChatServiceProvider
|
*/

Route::get('/', function () {
    return view('welcome');
});

