<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOsobaRequest;
use App\Http\Requests\UpdateOsobaRequest;
use App\Models\Osoba;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OsobaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Osoba::class);

        return view('osoby.index', [
            'osoby' => auth()->user()
                ->osoby()
                ->with(['aktivniClenstviCmt', 'clenstviCmt'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Osoba::class);

        return view('osoby.create');
    }

    public function store(StoreOsobaRequest $request): RedirectResponse
    {
        Gate::authorize('create', Osoba::class);

        $request->user()->osoby()->create([
            'jmeno' => $request->string('jmeno')->toString(),
            'prijmeni' => $request->string('prijmeni')->toString(),
            'datum_narozeni' => $request->date('datum_narozeni'),
            'staj' => $request->string('staj')->toString(),
            'gdpr_souhlas' => true,
            'gdpr_souhlas_at' => now(),
            'gdpr_odvolano' => false,
        ]);

        return redirect()
            ->route('osoby.index')
            ->with('status', 'osoba-created');
    }

    public function edit(Osoba $osoba): View
    {
        Gate::authorize('update', $osoba);

        return view('osoby.edit', [
            'osoba' => $osoba,
        ]);
    }

    public function update(UpdateOsobaRequest $request, Osoba $osoba): RedirectResponse
    {
        Gate::authorize('update', $osoba);

        $gdprOdvolano = (bool) $request->boolean('gdpr_odvolano');
        $gdprSouhlas = $gdprOdvolano ? false : (bool) $request->boolean('gdpr_souhlas', true);

        $osoba->update([
            'jmeno' => $request->string('jmeno')->toString(),
            'prijmeni' => $request->string('prijmeni')->toString(),
            'datum_narozeni' => $request->date('datum_narozeni'),
            'staj' => $request->string('staj')->toString(),
            'gdpr_souhlas' => $gdprSouhlas,
            'gdpr_odvolano' => $gdprOdvolano,
            'gdpr_souhlas_at' => $gdprSouhlas ? ($osoba->gdpr_souhlas_at ?? now()) : null,
        ]);

        return redirect()
            ->route('osoby.index')
            ->with('status', 'osoba-updated');
    }

    public function destroy(Osoba $osoba): RedirectResponse
    {
        Gate::authorize('delete', $osoba);

        $osoba->delete();

        return redirect()
            ->route('osoby.index')
            ->with('status', 'osoba-deleted');
    }
}
