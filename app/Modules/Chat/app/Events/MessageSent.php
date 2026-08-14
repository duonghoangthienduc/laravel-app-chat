<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Models\Message;
use Modules\Chat\Transformers\MessageResource;

class MessageSent implements ShouldBroadcastNow{

	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 */
	public function __construct(public readonly Message $message){
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return array<int, Channel>
	 */
	public function broadcastOn()
	: array{
		$this->message->loadMissing('conversation.participants');

		$channels = [new PrivateChannel('conversation.' . $this->message->conversation_id)];

		foreach ($this->message->conversation->participants as $participant){
			$channels[] = new PrivateChannel('App.Models.User.' . $participant->user_id);
		}

		return $channels;
	}

	public function broadcastAs()
	: string{
		return 'message.sent';
	}

	public function broadcastWith()
	: array{
		$this->message->loadMissing('sender:id,name');

		return new MessageResource($this->message)->resolve();
	}
}
