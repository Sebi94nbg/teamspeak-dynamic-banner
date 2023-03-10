<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerTemplateAddRequest;
use App\Http\Requests\BannerTemplateDisableEnableRequest;
use App\Http\Requests\BannerTemplateRemoveRequest;
use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Template;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BannerTemplateController extends Controller
{
    /**
     * Display the edit view.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        try {
            $banner = Banner::findOrFail($request->banner_id);
        } catch (ModelNotFoundException) {
            return Redirect::route('banners')
                ->withInput($request->all())
                ->with([
                    'error' => 'banner-not-found',
                    'message' => 'The banner templates, which you have tried to edit, does not exist.',
                ]);
        }

        return view('banner.template', ['banner_id' => $banner->id])->with([
            'banner' => $banner,
            'templates' => Template::all(),
        ]);
    }

    /**
     * Adds a template to a banner configuration.
     */
    public function add(BannerTemplateAddRequest $request): RedirectResponse
    {
        $request->validated();

        $banner_template = BannerTemplate::create([
            'banner_id' => $request->banner_id,
            'template_id' => $request->template_id,
        ]);

        if (! $banner_template->save()) {
            return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
                'error' => 'banner-template-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
            'success' => 'banner-template-add-successful',
            'message' => 'Successfully added the template to the banner.',
        ]);
    }

    /**
     * Removes a template from a banner configuration.
     */
    public function remove(BannerTemplateRemoveRequest $request): RedirectResponse
    {
        $request->validated();

        $banner_template = BannerTemplate::where([
            'banner_id' => $request->banner_id,
            'template_id' => $request->template_id,
        ])->first();

        if (! $banner_template->delete()) {
            return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
                'error' => 'banner-template-remove-error',
                'message' => 'Failed to delete the data set from the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
            'success' => 'banner-template-remove-successful',
            'message' => 'Successfully removed the template from the banner.',
        ]);
    }

    /**
     * Disables a template of a banner configuration.
     */
    public function disable(BannerTemplateDisableEnableRequest $request): RedirectResponse
    {
        $request->validated();

        $banner_template = BannerTemplate::where([
            'banner_id' => $request->banner_id,
            'template_id' => $request->template_id,
        ])->first();

        $banner_template->enabled = false;

        if (! $banner_template->save()) {
            return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
                'error' => 'banner-template-disable-error',
                'message' => 'Failed to update the data set from the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
            'success' => 'banner-template-disable-successful',
            'message' => 'Successfully disabled the template of the banner.',
        ]);
    }

    /**
     * Enables a template of a banner configuration.
     */
    public function enable(BannerTemplateDisableEnableRequest $request): RedirectResponse
    {
        $request->validated();

        $banner_template = BannerTemplate::where([
            'banner_id' => $request->banner_id,
            'template_id' => $request->template_id,
        ])->first();

        $banner_template->enabled = true;

        if (! $banner_template->save()) {
            return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
                'error' => 'banner-template-enable-error',
                'message' => 'Failed to update the data set from the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.templates', ['banner_id' => $request->banner_id])->with([
            'success' => 'banner-template-enable-successful',
            'message' => 'Successfully enabled the template of the banner.',
        ]);
    }
}
