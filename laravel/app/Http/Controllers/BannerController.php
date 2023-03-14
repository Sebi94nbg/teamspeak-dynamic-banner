<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerAddRequest;
use App\Http\Requests\BannerUpdateRequest;
use App\Models\Banner;
use App\Models\Instance;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Display the main page.
     */
    public function overview(): View
    {
        return view('banners')->with('banners', Banner::all());
    }

    /**
     * Display the add view.
     */
    public function add(): View
    {
        return view('banner.add')->with('instance_list', Instance::all());
    }

    /**
     * Save a new data set.
     */
    public function save(BannerAddRequest $request): RedirectResponse
    {
        $request->validated();

        $banner = Banner::create([
            'name' => $request->name,
            'instance_id' => $request->instance_id,
            'random_rotation' => ($request->has('random_rotation')) ? true : false,
        ]);

        if (! $banner->save()) {
            return Redirect::route('banner.add')->withInput($request->all())->with([
                'error' => 'banner-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        return Redirect::route('banner.edit', ['banner_id' => $banner->id])->with([
            'success' => 'banner-add-successful',
            'message' => 'Successfully added the new banner.',
        ]);
    }

    /**
     * Display the edit view.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        try {
            $banner = Banner::findOrFail($request->banner_id);
        } catch (ModelNotFoundException) {
            return Redirect::route('banners')
                ->with([
                    'error' => 'banner-not-found',
                    'message' => 'The banner, which you have tried to edit, does not exist.',
                ]);
        }

        return view('banner.edit', ['banner_id' => $banner->id])->with([
            'banner' => $banner,
            'instance_list' => Instance::all(),
            'banner_configurations' => $banner->configurations,
        ]);
    }

    /**
     * Update an existing data set.
     */
    public function update(BannerUpdateRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            $banner = Banner::findOrFail($request->banner_id);
        } catch (ModelNotFoundException) {
            return view('instance.main')->with([
                'error' => 'instance-not-found',
                'message' => 'The instance, which you have tried to edit, does not exist.',
            ]);
        }

        $banner->name = $request->name;
        $banner->instance_id = $request->instance_id;
        $banner->random_rotation = ($request->has('random_rotation')) ? true : false;

        if (! $banner->save()) {
            return Redirect::route('banner.edit', ['banner_id' => $banner->id])
                ->withInput($request->all())
                ->with([
                    'error' => 'banner-edit-error',
                    'message' => 'Failed to update the database entry. Please try again.',
                ]);
        }

        return Redirect::route('banner.edit', ['banner_id' => $banner->id])->with([
            'success' => 'banner-edit-successful',
            'message' => 'Successfully updated the banner.',
        ]);
    }
}
