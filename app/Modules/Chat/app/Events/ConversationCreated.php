<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Models\Conversation;

class ConversationCreated implements ShouldBroadcastNow{

	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct(public readonly Conversation $conversation){
	}

	/**
	 * @return array<int, Channel>
	 */
	public function broadcastOn()
	: array{
		$this->conversation->loadMissing('participants.user:id,name');

		return $this->conversation->participants
			->map(
				fn($participant) => new PrivateChannel('App.Models.User.' . $participant->user_id))
			->all();
	}

	public function broadcastAs()
	: string{
		return 'conversation.created';
	}

	public function broadcastWith()
	: array{
		return [
			'id'                     => $this->conversation->uuid,
			'is_group'               => $this->conversation->is_group,
			'participants'           => $this->conversation->participants->map(fn($p) => [
				'id'   => $p->user_id,
				'name' => $p->user?->name,
			])->values(),
			'last_message'           => NULL,
			'last_message_at'        => NULL,
			'last_message_sender_id' => NULL,
		];
	}
}