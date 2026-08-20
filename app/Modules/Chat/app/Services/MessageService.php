<?php

namespace Modules\Chat\Services;

use Illuminate\Pagination\CursorPaginator;
use Modules\Chat\Events\MessageSent;
use Modules\Chat\Interfaces\ConversationRepositoryInterface;
use Modules\Chat\Interfaces\MessageRepositoryInterface;

readonly class MessageService{

	public function __construct(
		private MessageRepositoryInterface $messageRepository,
		private ConversationRepositoryInterface $conversationRepository){
	}

	public function getMessages(string $conversationId, int $limit = 30)
	: CursorPaginator{
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
