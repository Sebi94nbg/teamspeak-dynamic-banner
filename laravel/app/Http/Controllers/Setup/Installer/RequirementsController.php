<?php

namespace App\Http\Controllers\Setup\Installer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\SystemStatusController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RequirementsController extends Controller
{
    /**
     * Display the view.
     */
    public function show_view(): RedirectResponse|View
    {
        if (User::all()->count() > 0) {
            return Redirect::route('dashboard');
        }

        $system_status_helper = new SystemStatusController();
        $system_status = $system_status_helper->system_status_json(false);

        return view('setup.installer.requirements', [
            'system_status' => json_decode($system_status),
            'system_status_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $system_status),
            'system_status_danger_count' => preg_match_all("/\"severity\"\:\"danger\"/", $system_status),
        ]);
    }
}
