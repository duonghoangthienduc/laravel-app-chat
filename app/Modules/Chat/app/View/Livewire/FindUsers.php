<?php

namespace Modules\Chat\View\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
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
}
