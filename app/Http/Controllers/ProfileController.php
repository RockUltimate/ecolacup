<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyProfileRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validated = $request->validated();
        $name = trim((string) ($validated['name'] ?? ''));
        $jmeno = trim((string) ($validated['jmeno'] ?? ''));
        $prijmeni = trim((string) ($validated['prijmeni'] ?? ''));

        if ($name !== '' && ($jmeno === '' || $prijmeni === '')) {
            $parts = preg_split('/\s+/', $name, 2) ?: [];
            $jmeno = $jmeno !== '' ? $jmeno : ($parts[0] ?? $request->user()->jmeno);
            $prijmeni = $prijmeni !== '' ? $prijmeni : ($parts[1] ?? $request->user()->prijmeni);
        }

        if ($name === '') {
            $name = trim($jmeno.' '.$prijmeni);
        }

        $validated['name'] = $name !== '' ? $name : $request->user()->name;
        $validated['jmeno'] = $jmeno !== '' ? $jmeno : $request->user()->jmeno;
        $validated['prijmeni'] = $prijmeni !== '' ? $prijmeni : $request->user()->prijmeni;

        if ($request->has('gdpr_souhlas')) {
            $validated['gdpr_souhlas_at'] = now();
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(DestroyProfileRequest $request): RedirectResponse
    {

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
