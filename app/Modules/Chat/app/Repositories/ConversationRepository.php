<?php

namespace Modules\Chat\Repositories;

use App\Core\Repositories\AbstractRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Chat\Interfaces\ConversationRepositoryInterface;
use Modules\Chat\Models\Conversation;

class ConversationRepository extends AbstractRepository implements ConversationRepositoryInterface{

	public function __construct(
		Conversation $model,
	){
		parent::__construct($model);
	}

	public function findConversation(int $userId, int $participantId)
	: ?Conversation{
		return $this->model::query()
		                   ->where('is_group', FALSE)
		                   ->whereHas('participants', function ($query) use ($userId){
			                   $query->where('user_id', $userId);
		                   })
		                   ->whereHas('participants', function ($query) use ($participantId){
			                   $query->where('user_id', $participantId);
		                   })
		                   ->has('participants', '=', 2)
		                   ->first();
	}

	/**
	 * @throws \Throwable
	 */
	public function newPrivateChat(User $user, int $participantId)
	: Conversation{
		return DB::transaction(function () use ($user, $participantId){
			$conversation = $this->model::create([
				'is_group' => FALSE,
			]);

			$conversation->participants()->createMany([
				[
					'user_id' => $user->id,
				],
				[
					'user_id' => $participantId,
				],
			]);

			return $conversation;
		});
	}
}
