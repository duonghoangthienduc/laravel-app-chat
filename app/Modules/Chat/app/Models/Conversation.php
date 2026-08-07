<?php

namespace Modules\Chat\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
		'is_group',
	];

	protected function casts()
	: array{
		return [
			'is_group' => 'boolean',
		];
	}

	public function participants()
	: HasMany{
		return $this->hasMany(Participant::class, 'conversation_id', 'uuid');
	}
}