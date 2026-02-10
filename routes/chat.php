<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Chat Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});
