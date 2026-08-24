<?php

namespace Modules\Log\Listeners;

use Illuminate\Auth\Events\Login;
use Modules\Log\Services\ActivityService;

readonly class RecordLoginActivity{

	public function __construct(private readonly ActivityService $activityService){
	}

	public function handle(Login $event)
	: void{
		$this->activityService->recordAccess($event->user->id);
	}
}