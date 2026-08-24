<?php

namespace Modules\Log\Services;

use Modules\Log\Interfaces\ActivityRepositoryInterface;

readonly class ActivityService{

	public function __construct(
		private ActivityRepositoryInterface $activityRepository,
	){
	}

	public function recordAccess(int $userId)
	: void{
		$this->activityRepository->record($userId);
	}

	public function heatmapData(int $userId, int $days = 371)
	: array{
		return $this->activityRepository->getHeatmapData($userId, $days);
	}

}
