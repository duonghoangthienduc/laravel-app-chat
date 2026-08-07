<?php

namespace App\Modules\Chat\app\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Chat\Services\ConversationService;
use Modules\Chat\Services\UserService;

class FindUsers extends Component{

	use WithPagination;

	public string $search = '';

	public function render(UserService $userService)
	: View{
		$authUserId = Auth::id();
		$users      = $userService->getUserForChat($authUserId, $this->search);

		return view('chat::components.findusers', ['users' => $users]);
	}

	/**
	 * @throws \Throwable
	 */
	public function startConversation(int $userId, ConversationService $conversation)
	: void{
		$authUserId = Auth::id();

		// Find or create a conversation between these two users
		$startConversation = $conversation->getConversations($userId, $authUserId);

		$this->redirect(route('chat.inbox'));
	}
}
