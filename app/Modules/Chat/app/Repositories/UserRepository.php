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
	: User|null{
		return $this->model->where('id', $id)->firstOr(fn() => null);
	}

	public function getUserForChat($authUserId, $search)
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
