<?php

namespace Modules\Chat\Repositories;

use App\Core\Repositories\AbstractRepository;
use Modules\Chat\Interfaces\MessageMediaRepositoryInterface;
use Modules\Chat\Models\MessageMedia;

class MessageMediaRepository extends AbstractRepository implements MessageMediaRepositoryInterface{

	public function __construct(MessageMedia $model){
		parent::__construct($model);
	}

	public function attach(int $messageId, array $mediaIds)
	: void{
		if (empty($mediaIds)){
			return;
		}

		$rows = collect($mediaIds)->values()->map(fn($mediaId, $index) => [
			'message_id' => $messageId,
			'media_id'   => $mediaId,
			'sort_order' => $index,
			'created_at' => now(),
			'updated_at' => now(),
		])->all();

		$this->model::query()->insert($rows);
	}

	public function getMediaIdsForMessage(int $messageId)
	: array{
		return $this->model::query()
		                   ->where('message_id', $messageId)
		                   ->orderBy('sort_order')
		                   ->pluck('media_id')
		                   ->all();
	}

	public function getMediaIdsForMessages(array $messageIds)
	: array{
		if (empty($messageIds)){
			return [];
		}

		return $this->model::query()
		                   ->whereIn('message_id', $messageIds)
		                   ->orderBy('sort_order')
		                   ->get(['message_id', 'media_id'])
		                   ->groupBy('message_id')
		                   ->map(fn($rows) => $rows->pluck('media_id')->all())
		                   ->all();
	}
}
