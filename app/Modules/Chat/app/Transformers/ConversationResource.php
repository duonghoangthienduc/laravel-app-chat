<?php

namespace Modules\Chat\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource{

	/**
	 * Transform the resource into an array.
	 */
	public function toArray(Request $request)
	: array{
		$otherParticipant = $this->participants
			->firstWhere('user_id', '!=', $request->user()->id);

		return [
			'id'              => $this->uuid,
			'is_group'        => $this->is_group,
			'other_name'      => $this->is_group ? $this->conversation_name : $otherParticipant?->user?->name,
			'other_user_id'   => $otherParticipant?->user_id,
			'last_message'    => $this->whenLoaded('lastMessage',
				fn() => $this->lastMessage?->content),
			'last_message_at' => $this->last_message_at?->toISOString(),
		];
	}
}
