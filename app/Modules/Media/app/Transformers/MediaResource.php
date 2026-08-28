<?php

namespace Modules\Media\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Media\Interfaces\MediaStorageInterface;

class MediaResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		$storage = app(MediaStorageInterface::class);

		return [
			'id'            => $this->id,
			'type'          => $this->type,
			'original_name' => $this->original_name,
			'mime_type'     => $this->mime_type,
			'size'          => $this->size,
			'url'           => $storage->url($this->path),
		];
	}
}