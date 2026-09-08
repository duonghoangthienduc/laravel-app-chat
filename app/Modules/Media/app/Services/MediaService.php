<?php

namespace Modules\Media\Services;

use Illuminate\Http\UploadedFile;
use Modules\Media\Enums\MediaType;
use Modules\Media\Interfaces\MediaRepositoryInterface;
use Modules\Media\Interfaces\MediaServiceInterface;
use Modules\Media\Interfaces\MediaStorageInterface;
use Modules\Media\Models\Media;
use Modules\Media\Transformers\MediaResource;
use Throwable;

readonly class MediaService implements MediaServiceInterface{

	public function __construct(
		private MediaStorageInterface $storage,
		private MediaRepositoryInterface $repository,
	){
	}

	/**
	 * @throws \Throwable
	 */
	public function upload(
		UploadedFile $file,
		?int $userId = NULL,
		?MediaType $type = NULL,
		?string $context = NULL,
	)
	: Media{
		$type ??= $this->detectType($file);

		$directory = $context
			? sprintf('%s/%s/%s/%s', $context, $type->value, now()->format('Y'), now()->format('m'))
			: sprintf('%s/%s/%s', $type->value, now()->format('Y'), now()->format('m'));

		$path = $this->storage->store($file, $directory);

		try{
			return $this->repository->create([
				'disk'          => config('media.disk'),
				'path'          => $path,
				'original_name' => $file->getClientOriginalName(),
				'mime_type'     => $file->getMimeType(),
				'extension'     => $file->getClientOriginalExtension(),
				'size'          => $file->getSize(),
				'type'          => $type->value,
				'hash'          => hash_file('sha256', $file->getRealPath()),
				'metadata'      => $context ? ['context' => $context] : [],
				'created_by'    => $userId,
			]);
		}catch (Throwable $exception){
			$this->storage->delete($path);
			throw $exception;
		}
	}

	public function findMany(array $ids)
	: array{
		if (empty($ids)){
			return [];
		}

		$models = $this->repository->findMany($ids);

		return MediaResource::collection($models)->resolve();
	}


	private function detectType(UploadedFile $file)
	: MediaType{
		$mime = $file->getMimeType();

		return match (TRUE) {
			str_starts_with($mime, 'image/') => MediaType::IMAGE,
			str_starts_with($mime, 'video/') => MediaType::VIDEO,
			str_starts_with($mime, 'audio/') => MediaType::AUDIO,
			default => MediaType::OTHER,
		};
	}
}
