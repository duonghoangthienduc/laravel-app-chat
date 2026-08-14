<?php
// Modules/Chat/app/Support/ConversationNavResolver.php

namespace Modules\Chat\Support;

use App\Support\Navigation\ResolvesNavChildren;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Services\ConversationService;

readonly class ConversationNavResolver implements ResolvesNavChildren{

	private const COLOR_PAIRS = [
		['#6366f1', '#7c3aed'],
		['#f43f5e', '#f97316'],
		['#06b6d4', '#2563eb'],
		['#10b981', '#0d9488'],
		['#d946ef', '#db2777'],
		['#f59e0b', '#ea580c'],
	];

	public function __construct(
		private ConversationService $conversationService,
	){
	}

	public function resolve()
	: Collection{
		if (!Auth::check()){
			return collect();
		}

		return $this->conversationService
			->getConversationByUserId(Auth::id())
			->take(8)
			->map(function ($conversation){
				$name = $conversation->conversation_name
				        ?? $conversation->participants->firstWhere('user_id', '!=',
					Auth::id())?->user?->name
				           ?? 'Unknown';

				return [
					'id'           => $conversation->uuid,   // ← thêm dòng này
					'label'        => $name,
					'href'         => route('chat.show', ['conversation' => $conversation->uuid]),
					'active'       => request()->route('conversation') === $conversation->uuid,
					'initial'      => mb_strtoupper(mb_substr($name, 0, 1)),
					'avatar_style' => $this->avatarStyle($name),
				];
			});
	}

	private function avatarStyle(string $name)
	: string{
		$sum = array_sum(array_map('mb_ord', mb_str_split($name)));
		[$from, $to] = self::COLOR_PAIRS[$sum % count(self::COLOR_PAIRS)];

		return "background: linear-gradient(135deg, {$from}, {$to});";
	}
}