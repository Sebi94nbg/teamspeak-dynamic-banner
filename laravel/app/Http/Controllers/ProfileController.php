<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if (! $request->user()->save()) {
            return Redirect::route('profile.edit')
                ->withInput($request->all())
                ->with([
                    'error' => 'user-profile-edit-error',
                    'message' => 'Failed to update the profile information in the database. Please try again.',
                ]);
        }

        return Redirect::route('profile.edit')->with([
            'success' => 'user-profile-edit-successful',
            'message' => 'Successfully updated the profile.',
        ]);
    }

    /**
     * Update the user's password.
     */
    public function change_password(ChangePasswordRequest $request): RedirectResponse
    {
        $request->validated();

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return Redirect::route('profile.edit')
                ->with([
                    'error' => 'user-password-edit-error',
                    'message' => 'Your provided current password was incorrect.',
                ]);
        }

        $request->user()->password = Hash::make($request->password);

        if (! $request->user()->save()) {
            return Redirect::route('profile.edit')
                ->with([
                    'error' => 'user-password-edit-error',
                    'message' => 'Failed to update the password information in the database. Please try again.',
                ]);
        }

        return Redirect::route('profile.edit')->with([
            'success' => 'user-password-edit-successful',
            'message' => 'Successfully updated the password.',
        ]);
    }
}
