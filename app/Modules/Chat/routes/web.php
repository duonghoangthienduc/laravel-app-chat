<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;
use Modules\Chat\View\Livewire\FindUsers;

Route::middleware(['auth', 'verified'])->group(function (){
	Route::get('/chats', FindUsers::class)->name('chat');
	Route::get('/chat/inbox', [ChatController::class, 'index'])->name('chat.inbox');
});
