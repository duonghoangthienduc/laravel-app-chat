<?php

namespace Modules\Chat\Services;

use Modules\Chat\Events\MessageSent;
use Modules\Chat\Repositories\ConversationRepository;
use Modules\Chat\Repositories\MessageRepository;

class MessageService{

	public function __construct(
		private readonly MessageRepository $messageRepository,
		private readonly ConversationRepository $conversationRepository){
	}

	public function getMessages(string $conversationId, int $limit = 30){
		return $this->messageRepository->getForConversation($conversationId, $limit);
	}

	public function sendMessage(string $conversationId, int $senderId, string $content){
		$message = $this->messageRepository->create([
			'conversation_id' => $conversationId,
			'sender_id'       => $senderId,
			'content'         => $content,
			'status'          => 'sent',
		]);

		$this->conversationRepository->update($conversationId, [
			'last_message_at' => now(),
		]);

		event(new MessageSent($message));

		return $message->load('sender:id,name');
	}
}
