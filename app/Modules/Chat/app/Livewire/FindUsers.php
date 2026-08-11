<?php

namespace App\Modules\Chat\app\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Chat\Services\ConversationService;
use Modules\Chat\Services\UserService;
use Throwable;

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
	#[Title('New Conversations')]
	public function startConversation(int $userId, ConversationService $conversation)
	: void{
		try{
			$authUserId = Auth::id();
			$conversation->getConversations($userId, $authUserId);

			$this->redirect(route('chat.inbox'));
		}catch (Throwable $e){
			$this->dispatch('conversation-start-failed');
			throw $e; // vẫn giữ log/report lỗi bình thường
		}
	}
}
