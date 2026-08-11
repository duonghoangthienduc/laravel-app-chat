<?php

namespace Modules\Chat\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('message')]
class Message extends Model{

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'conversation_id',
		'sender_id',
		'content',
		'status',
	];

	public function conversation()
	: BelongsTo{
		return $this->belongsTo(Conversation::class, 'conversation_id', 'uuid');
	}

	public function sender()
	: BelongsTo{
		return $this->belongsTo(User::class, 'sender_id');
	}
}
