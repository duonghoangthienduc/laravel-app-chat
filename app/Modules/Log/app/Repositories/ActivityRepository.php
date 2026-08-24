<?php

namespace Modules\Log\Repositories;

use App\Core\Repositories\AbstractRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Log\Interfaces\ActivityRepositoryInterface;
use Modules\Log\Models\ActivityDay;

class ActivityRepository extends AbstractRepository implements ActivityRepositoryInterface{

	public function __construct(ActivityDay $model){
		parent::__construct($model);
	}

	public function record(int $userId)
	: void{
		$today = Carbon::today();

		$this->model::query()->upsert(
			[[
				'user_id'    => $userId,
				'date'       => $today,
				'count'      => 1,
				'created_at' => now(),
				'updated_at' => now(),
			]],
			uniqueBy: ['user_id', 'date'],
			update: ['count' => DB::raw('"activity_day"."count" + 1'), 'updated_at' => now()]
		);
	}

	public function getHeatmapData(int $userId, int $days)
	: array{
		$since = Carbon::today()->subDays($days);

		return $this->model::query()
		                   ->where('user_id', $userId)
		                   ->where('date', '>=', $since)
		                   ->pluck('count', 'date')
		                   ->mapWithKeys(fn($count, $date) => [Carbon::parse($date)
		                                                             ->toDateString() => $count])
		                   ->all();
	}
}
