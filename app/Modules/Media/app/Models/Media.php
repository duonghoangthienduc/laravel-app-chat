<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'media')]
class Media extends Model{

	protected $table = 'media';

	protected $fillable = [
		'disk', 'path', 'original_name', 'mime_type', 'extension',
		'size', 'type', 'hash', 'metadata', 'created_by',
	];

	protected $casts = [
		'metadata' => 'array',
	];
}
