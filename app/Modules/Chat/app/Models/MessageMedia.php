<?php

namespace Modules\Chat\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'message_media')]
class MessageMedia extends Model{

	protected $fillable = ['message_id', 'media_id', 'sort_order'];
}
