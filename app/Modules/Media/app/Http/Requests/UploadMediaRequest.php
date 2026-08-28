<?php

namespace Modules\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest{

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules()
	: array{
		return [
			'file'    => [
				'required',
				'file',
				'max:' . config('media.max_size_kb', 51200),
				'mimes:jpg,jpeg,png,gif,webp,mp4,mov,mp3,wav,pdf,zip',
			],
			'context' => ['nullable', 'string', 'max:40', 'regex:/^[a-z0-9\-]+$/'],
		];
	}

	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize()
	: bool{
		return TRUE;
	}
}
