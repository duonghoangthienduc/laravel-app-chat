<?php

namespace Modules\Media\Interfaces;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Support\Collection;

interface MediaRepositoryInterface extends RepositoryInterface{

	public function findMany(array $ids)
	: Collection;
}
