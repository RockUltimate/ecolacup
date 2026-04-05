<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOsobaRequest;
use App\Http\Requests\UpdateOsobaRequest;
use App\Models\Osoba;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OsobaController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Osoba::class);

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
        $this->authorize('create', Osoba::class);

        return view('osoby.create');
    }

    public function store(StoreOsobaRequest $request): RedirectResponse
    {
        $this->authorize('create', Osoba::class);

        $request->user()->osoby()->create([
            'jmeno' => $request->string('jmeno')->toString(),
            'prijmeni' => $request->string('prijmeni')->toString(),
            'datum_narozeni' => Carbon::createFromFormat('d.m.Y', $request->input('datum_narozeni'))->toDateString(),
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
        $this->authorize('update', $osoba);

        return view('osoby.edit', [
            'osoba' => $osoba,
        ]);
    }

    public function update(UpdateOsobaRequest $request, Osoba $osoba): RedirectResponse
    {
        $this->authorize('update', $osoba);

        $gdprOdvolano = (bool) $request->boolean('gdpr_odvolano');
        $gdprSouhlas = $gdprOdvolano ? false : (bool) $request->boolean('gdpr_souhlas', true);

        $osoba->update([
            'jmeno' => $request->string('jmeno')->toString(),
            'prijmeni' => $request->string('prijmeni')->toString(),
            'datum_narozeni' => Carbon::createFromFormat('d.m.Y', $request->input('datum_narozeni'))->toDateString(),
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
        $this->authorize('delete', $osoba);

        $osoba->delete();

        return redirect()
            ->route('osoby.index')
            ->with('status', 'osoba-deleted');
    }
}
