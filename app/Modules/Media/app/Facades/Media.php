<?php

namespace Modules\Media\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Media\Services\MediaService;

class Media extends Facade{

	protected static function getFacadeAccessor()
	: string{
		return MediaService::class;
	}
}