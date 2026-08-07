<?php

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements RepositoryInterface{

	public function __construct(
		protected Model $model,
	){
	}

	public function find(int|string $id)
	: ?Model{
		return $this->model->newQuery()->find($id);
	}

	public function findOrFail(int|string $id)
	: Model{
		return $this->model->newQuery()->findOrFail($id);
	}

	public function create(array $data)
	: Model{
		return $this->model->newQuery()->create($data);
	}

	public function update(int|string $id, array $data)
	: Model{
		$model = $this->findOrFail($id);

		$model->update($data);

		return $model->refresh();
	}

	public function delete(int|string $id)
	: bool{
		return (bool) $this->findOrFail($id)->delete();
	}
}