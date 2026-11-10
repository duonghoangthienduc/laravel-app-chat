<?php

namespace Modules\Media\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Media\Http\Requests\UploadMediaRequest;
use Modules\Media\Interfaces\MediaServiceInterface;
use Modules\Media\Transformers\MediaResource;

class MediaController extends Controller{

	public function __construct(
		private readonly MediaServiceInterface $mediaService,
	){
	}

	public function store(UploadMediaRequest $request)
	: MediaResource{
		$media = $this->mediaService->upload(
			$request->file('file'),
			Auth::id(),
			NULL,
			$request->input('context'),
		);

		return new MediaResource($media);
	}
}
