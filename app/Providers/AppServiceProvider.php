<?php

namespace App\Providers;

use App\Support\Navigation\Facades\Navigation;
use App\Support\Navigation\NavigationRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider{

	/**
	 * Register any application services.
	 */
	public function register()
	: void{
		$this->app->singleton(NavigationRegistry::class);
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot()
	: void{
		$this->configureDefaults();
		AliasLoader::getInstance()->alias('Navigation', Navigation::class);
	}

	/**
	 * Configure default behaviors for production-ready applications.
	 */
	protected function configureDefaults()
	: void{
		Date::use(CarbonImmutable::class);

		DB::prohibitDestructiveCommands(
			app()->isProduction(),
		);

		Password::defaults(fn()
		: ?Password => app()->isProduction()
			? Password::min(12)
			          ->mixedCase()
			          ->letters()
			          ->numbers()
			          ->symbols()
			          ->uncompromised()
			: NULL,
		);

		if (config('app.force_https')){
			URL::forceScheme('https');
		}
	}
}
