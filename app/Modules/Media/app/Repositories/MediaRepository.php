<?php

namespace Modules\Media\Repositories;

use App\Core\Repositories\AbstractRepository;
use Illuminate\Support\Collection;
use Modules\Media\Interfaces\MediaRepositoryInterface;
use Modules\Media\Models\Media;

class MediaRepository extends AbstractRepository implements MediaRepositoryInterface{

	public function __construct(Media $model){
		parent::__construct($model);
	}

	public function findMany(array $ids)
	: Collection{
		if (empty($ids)){
			return collect();
		}

		return $this->model::query()->whereIn('id', $ids)->get();
	}
}
