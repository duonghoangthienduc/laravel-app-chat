<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Services\ConversationService;
use Modules\Chat\Transformers\ConversationResource;

class ConversationController extends Controller{

	public function __construct(
		private readonly ConversationService $conversationService,
	){
	}

	public function index(Request $request){
		$conversations = $this->conversationService->getConversationByUserId($request->user()->id);

		return ConversationResource::collection($conversations);
	}

	public function markAsRead(Conversation $conversation, Request $request){
		//		$this->repository->markAsRead($conversation->conversation_id, $request->user()->id);

		return response()->noContent();
	}

	public function destroy(Conversation $conversation, Request $request){
		//		$this->repository->hideForUser($conversation->conversation_id, $request->user()->id);

		return response()->noContent();
	}
}