<?php

namespace Modules\Chat\Interfaces;

use App\Core\Contracts\RepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Chat\Models\Conversation;

interface ConversationRepositoryInterface extends RepositoryInterface{

	public function findConversation(int $userId, int $participantId)
	: ?Conversation;

	public function newPrivateChat(User $user, int $participantId)
	: Conversation;

	public function getConversationsByUser(int $userId)
	: Collection;
}