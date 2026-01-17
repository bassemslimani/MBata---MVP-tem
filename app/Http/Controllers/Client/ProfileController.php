<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the client's profile page.
     */
    public function edit(Request $request): \Inertia\Response
    {
        return Inertia::render('Client/Profile', [
            'auth' => [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                    'avatar' => $request->user()->avatar,
                    'locale' => $request->user()->locale,
                ],
            ],
        ]);
    }

    /**
     * Update the client's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'locale' => ['nullable', 'in:fr,ar,en'],
        ]);

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->back()
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the client's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Update the client's preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:fr,ar,en'],
            'email_notifications' => ['boolean'],
            'sms_notifications' => ['boolean'],
        ]);

        $request->user()->update([
            'locale' => $validated['locale'],
        ]);

        return redirect()->back()
            ->with('success', 'Preferences updated successfully.');
    }
}
