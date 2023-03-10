<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\BannerVariableController as HelpersBannerVariableController;
use App\Models\Banner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;
use Predis\Connection\ConnectionException;

class BannerVariableController extends Controller
{
    /**
     * Display the overview page.
     */
    public function overview(Request $request): View
    {
        try {
            $banner = Banner::findOrFail($request->banner_id);
        } catch (ModelNotFoundException) {
            return Redirect::route('banners')
                ->withInput($request->all())
                ->with([
                    'error' => 'banner-not-found',
                    'message' => 'The banner variables, which you have tried to edit, does not exist.',
                ]);
        }

        $redis_connection_error = null;
        $variables_and_values = [];
        try {
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner->instance->id.'_datetime'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner->instance->id.'_servergrouplist'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner->instance->id.'_virtualserver_info'));
        } catch (ConnectionException $connection_exception) {
            $redis_connection_error = $connection_exception->getMessage();
            $variables_and_values = [];
        }

        $banner_variable_helper = new HelpersBannerVariableController(null);
        $variables_and_values = array_merge($variables_and_values, $banner_variable_helper->get_client_specific_info_from_cache($banner->instance, $request->ip()));

        return view('banner.variables')->with([
            'redis_connection_error' => $redis_connection_error,
            'variables_and_values' => $variables_and_values,
        ]);
    }
}
