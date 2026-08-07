<?php

namespace Modules\Chat\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Chat\Repositories\UserRepository;

final readonly class UserService{

	public function __construct(
		private UserRepository $user,
	){
	}

	public function getUserForChat(int $authUserId, string $search)
	: LengthAwarePaginator{
		return $this->user->getUserForChat($authUserId, $search);
	}
}
