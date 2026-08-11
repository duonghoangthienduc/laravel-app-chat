<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\Chat\Models\Conversation;

Broadcast::channel('App.Models.User.{id}', function ($user, $id){
	return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, string $conversationId){
	return Conversation::query()
	                   ->whereKey($conversationId)
	                   ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
	                   ->exists();
});