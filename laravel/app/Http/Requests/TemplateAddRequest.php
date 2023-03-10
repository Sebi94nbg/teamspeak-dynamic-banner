<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateAddRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'alias' => ['required', 'string'],
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg', 'dimensions:min_width=468,min_height=60,max_width=1024,max_height=300', 'max:5120'],
        ];
    }
}
