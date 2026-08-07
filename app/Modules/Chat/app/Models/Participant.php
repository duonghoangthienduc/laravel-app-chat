<?php

namespace Modules\Chat\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('participant')]
class Participant extends Model{

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'conversation_id',
		'user_id',
	];

	public function user()
	: BelongsTo{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function conversation()
	: BelongsTo{
		return $this->belongsTo(Conversation::class, 'conversation_id', 'uuid');
	}
}