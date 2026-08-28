<?php

namespace Modules\Media\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Interfaces\MediaStorageInterface;

class LocalMediaStorage implements MediaStorageInterface{

	public function store(UploadedFile $file, string $directory)
	: string{
		return $file->store($directory, config('media.disk'));
	}

	public function delete(string $path)
	: bool{
		return Storage::disk(config('media.disk'))->delete($path);
	}

	public function url(string $path)
	: string{
		return Storage::disk(config('media.disk'))->url($path);
	}
}