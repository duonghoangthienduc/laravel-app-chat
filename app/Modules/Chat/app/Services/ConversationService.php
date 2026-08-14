<?php

namespace Modules\Chat\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Chat\Events\ConversationCreated;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Repositories\ConversationRepository;
use Modules\Chat\Repositories\UserRepository;

readonly class ConversationService{

	public function __construct(
		private ConversationRepository $conversationRepository,
		private UserRepository $userRepository
	){
	}

	/**
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function getConversations(int $authId, int $userId)
	: Conversation{
		$user = $this->userRepository->getUserById($userId);

		if (!$user){
			throw new Exception("User not found");
		}

		$conversation = $this->conversationRepository->findConversation($user->id, $authId);

		if (!$conversation){
			$conversation = $this->conversationRepository->newPrivateChat($user, $authId);

			event(new ConversationCreated($conversation));
		}

		return $conversation;
	}

	public function getConversationById(string $uuid)
	: ?Model{
		return $this->conversationRepository->find($uuid);
	}

	public function getConversationByUserId(int $authId)
	: Collection{
		return $this->conversationRepository->getConversationsByUser($authId);
	}
}