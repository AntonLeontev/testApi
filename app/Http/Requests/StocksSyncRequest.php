<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StocksSyncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
			'*'					  => ['required', 'array'],
            '*.uuid'		      => ['required', 'uuid'],
            '*.stocks' 			  => ['required', 'array'],
			'*.stocks.*.uuid'	  => ['required', 'uuid'],
			'*.stocks.*.quantity' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ];
    }
}
