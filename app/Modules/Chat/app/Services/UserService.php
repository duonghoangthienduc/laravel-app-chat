<?php

namespace Modules\Chat\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Chat\Interfaces\UserRepositoryInterface;

final readonly class UserService{

	public function __construct(
		private UserRepositoryInterface $user,
	){
	}

	public function getUserForChat(int $authUserId, string $search)
	: LengthAwarePaginator{
		return $this->user->getUserForChat($authUserId, $search);
	}
}
