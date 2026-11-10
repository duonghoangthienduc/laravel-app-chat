<?php

namespace Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Modules\OptionalModule;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Services\ConversationService;
use Modules\Chat\Services\MessageService;
use Modules\Chat\Transformers\ConversationResource;

class ChatController extends Controller{

	public function __construct(
		private readonly ConversationService $conversationService,
		private readonly MessageService $messageService,
	){
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index(){
		return view('chat::index', [
			'activeConversationId' => NULL
		]);
	}

	/**
	 * Show the specified resource.
	 */
	public function show(string $uuid){
		$conversation = $this->conversationService->getConversationById($uuid);

		abort_if(
			!$conversation->participants->contains('user_id', Auth::id()),
			403
		);

		return view('chat::pages.show', [
			'activeConversationId' => $conversation->uuid,
			'activeConversation'   => new ConversationResource($conversation),
			'initialMedia'         => OptionalModule::isActive('Media'),
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id){
		return view('chat::edit');
	}
}
