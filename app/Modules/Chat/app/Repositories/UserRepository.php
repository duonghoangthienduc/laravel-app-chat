<?php

namespace Modules\Chat\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class UserRepository{

	public function __construct(
		private User $model,
	){

	}

	public function getUserById(int $id)
	: ?User{
		return $this->model->where('id', $id)->firstOr(fn() => NULL);
	}

	public function getUserForChat(int $authUserId, string $search)
	: LengthAwarePaginator{
		return $this->model::query()
		                   ->where('id', '!=', $authUserId)
		                   ->when($search, function ($query) use ($search){
			                   $query->where(function ($q) use ($search){
				                   $q->where('name', 'like', "%{$search}%")
				                     ->orWhere('email', 'like', "%{$search}%");
			                   });
		                   })->orderBy('name')
		                   ->paginate(20);
	}
}
