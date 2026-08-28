<?php

namespace App\Support\Modules;

use Nwidart\Modules\Facades\Module;

class OptionalModule{

	public static function isActive(string $name)
	: bool{
		return Module::has($name) && Module::isEnabled($name);
	}
}