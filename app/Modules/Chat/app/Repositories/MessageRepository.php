<?php

namespace Modules\Chat\Repositories;

use App\Core\Repositories\AbstractRepository;
use Illuminate\Pagination\CursorPaginator;
use Modules\Chat\Interfaces\MessageRepositoryInterface;
use Modules\Chat\Models\Message;

class MessageRepository extends AbstractRepository implements MessageRepositoryInterface{

	public function __construct(
		Message $model,
	){
		parent::__construct($model);
	}

	public function getForConversation(string $conversationId, int $limit = 30)
	: CursorPaginator{
		return $this->model::query()
		                   ->where('conversation_id', $conversationId)
		                   ->with('sender:id,name')
		                   ->latest()
		                   ->cursorPaginate($limit);
	}
}
