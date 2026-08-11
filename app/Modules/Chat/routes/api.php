<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\Api\ConversationController;
use Modules\Chat\Http\Controllers\Api\MessageController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function (){
	Route::get('chat/conversations', [ConversationController::class, 'index']);
	Route::get('chat/conversations/{conversation}/messages', [MessageController::class, 'index']);
	Route::post('chat/conversations/{conversation}/messages', [MessageController::class, 'store']);
	Route::patch('conversations/{conversation}/read',
		[ConversationController::class, 'markAsRead']);
	Route::delete('conversations/{conversation}', [ConversationController::class, 'destroy']);
});
