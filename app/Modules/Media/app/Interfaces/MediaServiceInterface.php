<?php

namespace Modules\Media\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Modules\Media\Enums\MediaType;

interface MediaServiceInterface{

	public function upload(
		UploadedFile $file,
		?int $userId = NULL,
		?MediaType $type = NULL,
		?string $context = NULL,
	)
	: Model;

	public function findMany(array $ids)
	: array;
}
