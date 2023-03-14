<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'instance_id' => ['required', 'integer', 'exists:App\Models\Instance,id'],
            'random_rotation' => ['sometimes'],
        ];
    }
}
