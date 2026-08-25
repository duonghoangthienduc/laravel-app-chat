<?php

namespace Modules\Log\Providers;

use App\Support\Dashboard\Facades\Dashboard;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Modules\Log\Http\Middleware\TrackActivity;
use Modules\Log\Interfaces\ActivityRepositoryInterface;
use Modules\Log\Listeners\RecordLoginActivity;
use Modules\Log\Repositories\ActivityRepository;
use Modules\Log\Services\ActivityService;
use Nwidart\Modules\Support\ModuleServiceProvider;

class LogServiceProvider extends ModuleServiceProvider{

	/**
	 * The name of the module.
	 */
	protected string $name = 'Log';

	/**
	 * The lowercase version of the module name.
	 */
	protected string $nameLower = 'log';

	/**
	 * Command classes to register.
	 *
	 * @var string[]
	 */
	// protected array $commands = [];

	/**
	 * Provider classes to register.
	 *
	 * @var string[]
	 */
	protected array $providers = [
		EventServiceProvider::class,
		RouteServiceProvider::class,
	];

	/**
	 * Define module schedules.
	 *
	 * @param $schedule
	 */
	// protected function configureSchedules(Schedule $schedule): void
	// {
	//     $schedule->command('inspire')->hourly();
	// }

	public $bindings = [
		ActivityRepositoryInterface::class => ActivityRepository::class,
	];

	public function boot()
	: void{
		parent::boot();

		Route::aliasMiddleware('track.activity', TrackActivity::class);

		Event::listen(Login::class, RecordLoginActivity::class);

		Blade::anonymousComponentPath(
			module_path('Log', 'resources/views/components'),
			'log'
		);

		Dashboard::register(
			view: 'log::components.activity-heatmap',
			data: function (){
				$service = $this->app->make(ActivityService::class);

				return [
					'data'  => $service->heatmapData(Auth::id()),
					'today' => Carbon::today()->toDateString(),
				];
			}
		);
	}
}
