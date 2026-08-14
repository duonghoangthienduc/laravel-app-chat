<?php

namespace Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Services\ConversationService;

class ChatController extends Controller{

	public function __construct(
		private readonly ConversationService $conversationService,
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
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id){
		return view('chat::edit');
	}
}
