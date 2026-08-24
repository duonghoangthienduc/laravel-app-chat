<?php

namespace Modules\Log\Interfaces;

use App\Core\Contracts\RepositoryInterface;

interface ActivityRepositoryInterface extends RepositoryInterface{

	public function record(int $userId)
	: void;

	/**
	 * @return array<string, int> date (Y-m-d) => count
	 */
	public function getHeatmapData(int $userId, int $days)
	: array;
}
