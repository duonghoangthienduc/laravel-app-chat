<?php

namespace Modules\Media\Providers;

use Modules\Media\Interfaces\MediaRepositoryInterface;
use Modules\Media\Interfaces\MediaServiceInterface;
use Modules\Media\Interfaces\MediaStorageInterface;
use Modules\Media\Repositories\MediaRepository;
use Modules\Media\Services\MediaService;
use Modules\Media\Storage\LocalMediaStorage;
use Nwidart\Modules\Support\ModuleServiceProvider;

class MediaServiceProvider extends ModuleServiceProvider{

	/**
	 * The name of the module.
	 */
	protected string $name = 'Media';

	/**
	 * The lowercase version of the module name.
	 */
	protected string $nameLower = 'media';

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
		MediaStorageInterface::class    => LocalMediaStorage::class,
		MediaRepositoryInterface::class => MediaRepository::class,
		MediaServiceInterface::class    => MediaService::class,
	];


	public function register()
	: void{
		parent::register();

		$this->app->singleton(
			MediaServiceInterface::class,
			MediaService::class
		);
	}
}
