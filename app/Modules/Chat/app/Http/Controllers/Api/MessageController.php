<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Services\ConversationService;
use Modules\Chat\Services\MessageService;
use Modules\Chat\Transformers\MessageResource;

class MessageController extends Controller{

	public function __construct(
		private readonly MessageService $messageService,
		private readonly ConversationService $conversationService,
	){

	}

	/**
	 * Display a listing of the resource.
	 */
	public function index(string $conversation){
		$conversationModel = $this->conversationService->getConversationById($conversation);

		abort_if(
			!$conversationModel->participants->contains('user_id', Auth::id()),
			403
		);

		return MessageResource::collection(
			$this->messageService->getMessages($conversation)
		);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(){
		return view('chat::create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(string $conversation, Request $request){
		$conversationModel = $this->conversationService->getConversationById($conversation);

		abort_if(
			!$conversationModel->participants->contains('user_id', Auth::id()),
			403
		);

		$validated = $request->validate(['content' => 'required|string|max:5000']);

		$message = $this->messageService->sendMessage(
			$conversation,
			Auth::id(),
			$validated['content'],
		);

		return new MessageResource($message);
	}

	/**
	 * Show the specified resource.
	 */
	public function show($id){
		return view('chat::show');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id){
		return view('chat::edit');
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, $id){ }

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy($id){ }
}
