<?php

namespace Modules\Log\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('activity_day')]
class ActivityDay extends Model{

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = ['user_id', 'date', 'count'];

	protected function casts()
	: array{
		return ['date' => 'datetime'];
	}

	public function user()
	: BelongsTo{
		return $this->belongsTo(User::class);
	}

}
