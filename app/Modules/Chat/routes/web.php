<?php

use App\Modules\Chat\app\Livewire\FindUsers;
use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;

Route::middleware(['auth', 'verified', 'track.activity'])->group(function (){
	Route::get('/chats', FindUsers::class)->name('chat');
	Route::get('/chat/inbox', [ChatController::class, 'index'])->name('chat.inbox');
	Route::get('/chat/inbox/{conversation}', [ChatController::class, 'show'])->name('chat.show');
});