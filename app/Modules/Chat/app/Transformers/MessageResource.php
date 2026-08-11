<?php

namespace Modules\Chat\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource{

	/**
	 * Transform the resource into an array.
	 */
	public function toArray(Request $request)
	: array{
		return [
			'id'              => $this->id,
			'conversation_id' => $this->conversation_id,
			'sender_id'       => $this->sender_id,
			'sender_name'     => $this->whenLoaded('sender', fn() => $this->sender?->name),
			'content'         => $this->content,
			'status'          => $this->status,
			'created_at'      => $this->created_at?->format('g:i a'),
		];
	}
}
