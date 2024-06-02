<?php

namespace App\Http\Requests;

use App\Models\Url;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUrlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $url = Url::findOrFail($this->route('id'));
        return [
            'original_url' => 'nullable|url',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('urls')->ignore($url->id),
            ],
        ];
    }
}
