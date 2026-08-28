<?php

namespace Modules\Chat\Interfaces;

use App\Core\Contracts\RepositoryInterface;

interface MessageMediaRepositoryInterface extends RepositoryInterface{

	public function attach(int $messageId, array $mediaIds)
	: void;

	/** @return int[] */
	public function getMediaIdsForMessage(int $messageId)
	: array;

	/**
	 * @param int[] $messageIds
	 *
	 * @return array<int, int[]> message_id => [media_id, ...]
	 */
	public function getMediaIdsForMessages(array $messageIds)
	: array;
}
