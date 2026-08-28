<?php

namespace Modules\Chat\Http\Requests;

use App\Support\Modules\OptionalModule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest{

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules()
	: array{
		$mediaActive = OptionalModule::isActive('Media');

		return [
			'content'     => ['nullable', 'string', 'max:10000'],
			'media_ids'   => [$mediaActive ? 'nullable' : 'prohibited', 'array', 'max:10'],
			'media_ids.*' => $mediaActive ? ['integer', 'exists:media,id'] : [],
		];
	}

	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function withValidator(Validator $validator)
	: void{
		$validator->after(function (Validator $validator){
			if (!$this->filled('content') && !$this->filled('media_ids')){
				$validator->errors()->add('content', __('A message needs text or an image.'));
			}
		});
	}
}
