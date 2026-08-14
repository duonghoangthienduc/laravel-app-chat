<?php
// app/Support/Navigation/Facades/Navigation.php

namespace App\Support\Navigation\Facades;

use App\Support\Navigation\NavigationRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection groups()
 */
class Navigation extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return NavigationRegistry::class;
	}
}