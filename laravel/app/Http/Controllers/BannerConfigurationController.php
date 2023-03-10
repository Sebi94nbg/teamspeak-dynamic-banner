<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\DrawTextOnTemplateController;
use App\Http\Requests\BannerConfigurationUpsertRequest;
use App\Models\BannerConfiguration;
use App\Models\BannerTemplate;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BannerConfigurationController extends Controller
{
    /**
     * Display the edit view.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $banner_template = BannerTemplate::where(['banner_id' => $request->banner_id, 'template_id' => $request->template_id])->first();

        if (is_null($banner_template)) {
            return Redirect::route('banners')
                ->withInput($request->all())
                ->with([
                    'error' => 'banner-not-found',
                    'message' => 'The banner configuration, which you have tried to edit, does not exist.',
                ]);
        }

        return view('banner.configuration')->with([
            'banner_template' => $banner_template,
        ]);
    }

    /**
     * Upserts the banner template configuration.
     */
    public function upsert(BannerConfigurationUpsertRequest $request): View|RedirectResponse
    {
        $request->validated();

        try {
            $banner_template = BannerTemplate::findOrFail($request->banner_template_id);
        } catch (ModelNotFoundException) {
            return Redirect::route('banners')
                ->withInput($request->all())
                ->with([
                    'error' => 'banner-template-not-found',
                    'message' => 'The banner template configuration, which you have tried to update, does not exist.',
                ]);
        }

        $banner_configurations = [];

        for ($i = 0; $i < count($request->font_color_in_hexadecimal); $i++) {
            if (isset($configuration)) {
                unset($configuration);
            }

            $configuration['banner_template_id'] = $request->banner_template_id;
            $configuration['x_coordinate'] = $request->x_coordinate[$i];
            $configuration['y_coordinate'] = $request->y_coordinate[$i];
            $configuration['text'] = $request->text[$i];
            $configuration['font_size'] = $request->font_size[$i];
            $configuration['font_color_in_hexadecimal'] = $request->font_color_in_hexadecimal[$i];
            $configuration['id'] = isset($request->banner_configuration_id[$i]) ? $request->banner_configuration_id[$i] : null;

            $banner_configurations[] = $configuration;
        }

        if (! BannerConfiguration::upsert($banner_configurations, ['id'], [
            'banner_template_id',
            'x_coordinate',
            'y_coordinate',
            'text',
            'font_size',
            'font_color_in_hexadecimal',
        ])) {
            return view('banner.configuration')->with([
                'banner_template' => $banner_template,
                'error' => 'banner-template-upsert-error',
                'message' => 'Failed to add or update the data set in the database. Please try again.',
            ]);
        }

        $draw_text_on_template_helper = new DrawTextOnTemplateController();

        try {
            $draw_text_on_template_helper->draw_text_to_image($banner_template, true, false, $request->ip());
        } catch (Exception $exception) {
            return view('banner.configuration')->with([
                'banner_template' => $banner_template,
                'error' => 'banner-template-draw-text-to-image-error',
                'message' => "Failed to draw the text to the template: $exception",
            ]);
        }

        return view('banner.configuration')->with([
            'banner_template' => $banner_template,
            'success' => 'banner-template-upsert-success',
            'message' => 'Successfully added or updated the data set in the database.',
        ]);
    }

    /**
     * Deletes a single banner configuration.
     */
    public function delete(Request $request): RedirectResponse
    {
        try {
            $banner_configuration = BannerConfiguration::findOrFail($request->banner_configuration_id);
        } catch (ModelNotFoundException) {
            return redirect()->back()->with([
                'error' => 'banner-configuration-not-found',
                'message' => 'The banner configuration, which you have tried to delete, does not exist.',
            ]);
        }

        if (! $banner_configuration->delete()) {
            return redirect()->back()->with([
                'error' => 'banner-configuration-delete-error',
                'message' => 'Failed to delete the banner configuration from the database. Please try again.',
            ]);
        }

        return redirect()->back()->with([
            'success' => 'banner-configuration-delete-successful',
            'message' => 'Successfully deleted the banner configuration.',
        ]);
    }
}
