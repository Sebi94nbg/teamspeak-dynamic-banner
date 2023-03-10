<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstanceAddRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'host' => ['required', 'string', 'max:255'],
            'voice_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'serverquery_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'serverquery_username' => ['required', 'string'],
            'serverquery_password' => ['required', 'string'],
            'client_nickname' => ['required', 'string', 'max:30'],
        ];
    }
}
