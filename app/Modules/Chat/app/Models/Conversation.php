<?php

namespace Modules\Chat\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table('conversation')]
class Conversation extends Model{

	use HasUuids;

	protected $primaryKey = 'uuid';

	protected $keyType = 'string';

	public $incrementing = FALSE;

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'conversation_name',
		'last_message_at',
		'is_group'
	];

	protected function casts()
	: array{
		return [
			'is_group'        => 'boolean',
			'last_message_at' => 'datetime',
		];
	}

	public function participants()
	: HasMany{
		return $this->hasMany(Participant::class, 'conversation_id', 'uuid');
	}

	public function users()
	: HasManyThrough{
		return $this->hasManyThrough(
			User::class,
			Participant::class,
			'conversation_id',
			'id',
			'id',
			'user_id'
		);
	}

	public function lastMessage()
	: HasOne{
		return $this->hasOne(Message::class, 'conversation_id')->latestOfMany();
	}

	public function messages()
	: HasMany{
		return $this->hasMany(Message::class, 'conversation_id');
	}
}