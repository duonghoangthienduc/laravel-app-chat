<?php

namespace Modules\Chat\Providers;

use Modules\Chat\Interfaces\ConversationRepositoryInterface;
use Modules\Chat\Interfaces\MessageRepositoryInterface;
use Modules\Chat\Interfaces\ParticipantRepositoryInterface;
use Modules\Chat\Interfaces\UserRepositoryInterface;
use Modules\Chat\Repositories\ConversationRepository;
use Modules\Chat\Repositories\MessageRepository;
use Modules\Chat\Repositories\ParticipantRepository;
use Modules\Chat\Repositories\UserRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ChatServiceProvider extends ModuleServiceProvider{

	/**
	 * The name of the module.
	 */
	protected string $name = 'Chat';

	/**
	 * The lowercase version of the module name.
	 */
	protected string $nameLower = 'chat';

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
		ConversationRepositoryInterface::class => ConversationRepository::class,
		ParticipantRepositoryInterface::class  => ParticipantRepository::class,
		MessageRepositoryInterface::class      => MessageRepository::class,
		UserRepositoryInterface::class         => UserRepository::class,
	];
}
