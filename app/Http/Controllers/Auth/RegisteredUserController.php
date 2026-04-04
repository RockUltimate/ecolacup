<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
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
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'jmeno' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'prijmeni' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'datum_narozeni' => ['nullable', 'date'],
            'pohlavi' => ['nullable', 'in:M,F'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telefon' => ['nullable', 'string', 'max:30'],
            'gdpr_souhlas' => ['sometimes', 'accepted'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

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
            'datum_narozeni' => $request->date('datum_narozeni'),
            'pohlavi' => $request->string('pohlavi')->toString() ?: null,
            'email' => $request->email,
            'telefon' => $request->string('telefon')->toString() ?: null,
            'gdpr_souhlas' => $request->boolean('gdpr_souhlas', true),
            'gdpr_souhlas_at' => now(),
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
