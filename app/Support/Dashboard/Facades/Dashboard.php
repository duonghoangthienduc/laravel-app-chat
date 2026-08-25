<?php
// app/Support/Dashboard/Facades/Dashboard.php

namespace App\Support\Dashboard\Facades;

use App\Support\Dashboard\DashboardWidgetRegistry;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $view, Closure|array $data = [], int $priority = 0)
 * @method static Collection widgets()
 */
class Dashboard extends Facade{

	protected static function getFacadeAccessor()
	: string{
		return DashboardWidgetRegistry::class;
	}
}