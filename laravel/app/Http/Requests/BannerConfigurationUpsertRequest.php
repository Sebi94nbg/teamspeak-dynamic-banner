<?php

namespace App\Http\Requests;

use App\Models\BannerTemplate;
use Illuminate\Foundation\Http\FormRequest;

class BannerConfigurationUpsertRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'banner_configuration_id.*' => ['sometimes', 'integer', 'exists:App\Models\BannerConfiguration,id'],
            'banner_template_id' => ['required', 'integer', 'exists:App\Models\BannerTemplate,id'],
            'x_coordinate.*' => ['required', 'integer', 'min:0', 'max:'.BannerTemplate::findOrFail($this->banner_template_id)->template->width],
            'y_coordinate.*' => ['required', 'integer', 'min:0', 'max:'.BannerTemplate::findOrFail($this->banner_template_id)->template->height],
            'text.*' => ['required', 'string', 'max:255'],
            'font_size.*' => ['required', 'integer', 'min:1', 'max:5'],
            'font_color_in_hexadecimal.*' => ['required', 'string', 'regex:/^#[a-f0-9]{6}$/i'],
        ];
    }
}
