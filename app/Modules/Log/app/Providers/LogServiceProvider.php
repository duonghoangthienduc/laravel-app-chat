<?php

namespace Modules\Log\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Modules\Log\Http\Middleware\TrackActivity;
use Modules\Log\Interfaces\ActivityRepositoryInterface;
use Modules\Log\Listeners\RecordLoginActivity;
use Modules\Log\Repositories\ActivityRepository;
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
	}
}
