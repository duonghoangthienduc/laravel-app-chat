<?php

namespace Modules\Media\Interfaces;

use Illuminate\Http\UploadedFile;

interface MediaStorageInterface{

	public function store(UploadedFile $file, string $directory)
	: string;

	public function delete(string $path)
	: bool;

	public function url(string $path)
	: string;
}