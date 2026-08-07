<?php

namespace Modules\Chat\Services;

use Exception;
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
		}

		return $conversation;
	}
}
