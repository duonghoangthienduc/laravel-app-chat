<?php

namespace Modules\Chat\Interfaces;

use App\Core\Contracts\RepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends RepositoryInterface{

	public function getUserById(int $id)
	: ?User;

	public function getUserForChat(int $authUserId, string $search)
	: LengthAwarePaginator;
}