<?php

namespace Modules\Chat\Interfaces;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Pagination\CursorPaginator;

interface MessageRepositoryInterface extends RepositoryInterface{

	public function getForConversation(string $conversationId, int $limit = 30)
	: CursorPaginator;
}
