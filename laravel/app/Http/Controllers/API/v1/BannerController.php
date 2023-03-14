<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\DrawTextOnTemplateController;
use App\Models\Banner;
use App\Models\BannerTemplate;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Return the rendered template.
     */
    public function get_rendered_template(Request $request)
    {
        try {
            $banner = Banner::findOrFail(base_convert($request->banner_id, 35, 10));
        } catch (ModelNotFoundException) {
            return response('Invalid Banner ID in the URL.', 404);
        }

        $all_enabled_banner_templates = BannerTemplate::where(['banner_id' => $banner->id, 'enabled' => true])->get();

        if ($all_enabled_banner_templates->count() == 0) {
            return response('The banner does either not have any configured templates or all of them are disabled.', 401);
        }

        if ($banner->random_rotation) {
            $banner_template = $all_enabled_banner_templates->random(1)->first();
        } else {
            $current_minute = Carbon::now()->format('i');
            $display_duration_per_template = intval(60 / $all_enabled_banner_templates->count());
            $banner_template = $all_enabled_banner_templates[intval(ceil($current_minute / $display_duration_per_template)) - 1];
        }

        $banner_configurations = $banner_template->configurations;

        if (count($banner_configurations) == 0) {
            return response('The template does not have any configurations. This seems wrong.', 500);
        }

        // Return the generated image to the client
        $draw_text_on_template_helper = new DrawTextOnTemplateController();
        try {
            header('Content-Type: image/jpeg');
            $draw_text_on_template_helper->draw_text_to_image($banner_template, false, true, $request->ip());
        } catch (Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }
}
