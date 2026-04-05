<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreRegisteredUserRequest;
use App\Models\User;
use App\Support\CzechDate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(StoreRegisteredUserRequest $request): RedirectResponse
    {

        $jmeno = $request->string('jmeno')->toString();
        $prijmeni = $request->string('prijmeni')->toString();
        $name = $request->string('name')->toString();

        if ($name !== '' && ($jmeno === '' || $prijmeni === '')) {
            $parts = preg_split('/\s+/', trim($name), 2) ?: [];
            $jmeno = $jmeno !== '' ? $jmeno : ($parts[0] ?? $name);
            $prijmeni = $prijmeni !== '' ? $prijmeni : ($parts[1] ?? '');
        }

        $user = User::create([
            'name' => trim($name !== '' ? $name : ($jmeno.' '.$prijmeni)),
            'jmeno' => $jmeno,
            'prijmeni' => $prijmeni,
            'datum_narozeni' => CzechDate::toDateString($request->input('datum_narozeni')),
            'pohlavi' => $request->string('pohlavi')->toString() ?: null,
            'email' => $request->email,
            'telefon' => $request->string('telefon')->toString() ?: null,
            'gdpr_souhlas' => $request->boolean('gdpr_souhlas', true),
            'gdpr_souhlas_at' => now(),
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('udalosti.index', absolute: false));
    }
}
