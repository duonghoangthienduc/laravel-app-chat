<?php

namespace Modules\Chat\Services;

use App\Support\Modules\OptionalModule;
use Illuminate\Pagination\CursorPaginator;
use Modules\Chat\Events\MessageSent;
use Modules\Chat\Interfaces\ConversationRepositoryInterface;
use Modules\Chat\Interfaces\MessageMediaRepositoryInterface;
use Modules\Chat\Interfaces\MessageRepositoryInterface;
use Modules\Media\Interfaces\MediaServiceInterface;

//use Modules\Media\Facades\Media;

readonly class MessageService{

	public function __construct(
		private MessageRepositoryInterface $messageRepository,
		private MessageMediaRepositoryInterface $messageMediaRepository,
		private ConversationRepositoryInterface $conversationRepository,
		private MediaServiceInterface $mediaService,
	){
	}

	public function getMessages(string $conversationId, int $limit = 30)
	: CursorPaginator{
		$messages = $this->messageRepository->getForConversation($conversationId, $limit);

		$this->attachMediaToCollection($messages->getCollection());

		return $messages;
	}

	public function sendMessage(
		string $conversationId,
		int $senderId,
		?string $content,
		array $mediaIds = []){

		$message = $this->messageRepository->create([
			'conversation_id' => $conversationId,
			'sender_id'       => $senderId,
			'content'         => $content ?? '',
			'status'          => 'sent',
		]);

		if (!empty($mediaIds) && OptionalModule::isActive('Media')){
			$this->messageMediaRepository->attach($message->id, $mediaIds);
		}

		$this->conversationRepository->update($conversationId, [
			'last_message_at' => now(),
		]);

		$message->loadMissing('sender:id,name');
		$message->setAttribute('media_items', $this->resolveMediaForMessage($message->id));

		event(new MessageSent($message));

		return $message;
	}

	private function resolveMediaForMessage(int $messageId)
	: array{
		if (!OptionalModule::isActive('Media')){
			return [];
		}

		$mediaIds = $this->messageMediaRepository->getMediaIdsForMessage($messageId);

		return $this->mediaService->findMany($mediaIds);
	}

	private function attachMediaToCollection($messages)
	: void{
		if (!OptionalModule::isActive('Media')){
			$messages->each(fn($m) => $m->setAttribute('media_items', []));

			return;
		}

		$messageIds        = $messages->pluck('id')->all();
		$mediaIdsByMessage = $this->messageMediaRepository->getMediaIdsForMessages($messageIds);

		$allMediaIds   = collect($mediaIdsByMessage)->flatten()->unique()->values()->all();
		$resolvedMedia = collect($this->mediaService->findMany($allMediaIds))->keyBy('id');

		$messages->each(function ($message) use ($mediaIdsByMessage, $resolvedMedia){
			$ids = $mediaIdsByMessage[$message->id] ?? [];

			$message->setAttribute(
				'media_items',
				collect($ids)->map(fn($id) => $resolvedMedia->get($id))->filter()->values()->all()
			);
		});
	}
}
