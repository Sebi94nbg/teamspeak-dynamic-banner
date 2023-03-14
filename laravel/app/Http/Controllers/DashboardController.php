<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BannerConfiguration;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function dashboard(): View
    {
        return view('dashboard')->with([
            'instances_count' => Instance::all()->count(),
            'templates_count' => Template::all()->count(),
            'banners_count' => Banner::all()->count(),
            'banner_configurations_count' => BannerConfiguration::all()->count(),
        ]);
    }
}
