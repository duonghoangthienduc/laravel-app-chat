<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\Api\ConversationController;
use Modules\Chat\Http\Controllers\Api\MessageController;

Route::middleware(['auth:sanctum', 'track.activity'])->prefix('v1/chat')->group(function (){
	Route::get('conversations', [ConversationController::class, 'index']);
	Route::get('conversations/{conversation}/messages', [MessageController::class, 'index']);
	Route::post('conversations/{conversation}/messages', [MessageController::class, 'store']);
	Route::patch('conversations/{conversation}/read',
		[ConversationController::class, 'markAsRead']);
	Route::delete('conversations/{conversation}', [ConversationController::class, 'destroy']);
});
