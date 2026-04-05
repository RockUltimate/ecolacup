<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUdalostMoznostRequest;
use App\Http\Requests\Admin\StoreAdminUdalostRequest;
use App\Http\Requests\Admin\StoreAdminUdalostUstajeniRequest;
use App\Http\Requests\Admin\UpdateAdminUdalostRequest;
use App\Models\Udalost;
use App\Models\UdalostMoznost;
use App\Models\UdalostUstajeni;
use App\Support\CzechDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UdalostController extends Controller
{
    public function index(): View
    {
        return view('admin.udalosti.index', [
            'udalosti' => Udalost::query()->orderByDesc('datum_zacatek')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.udalosti.create');
    }

    public function show(Udalost $udalost): View
    {
        $udalost->loadCount([
            'prihlasky as active_prihlasky_count' => fn ($query) => $query->where('smazana', false),
            'prihlasky as deleted_prihlasky_count' => fn ($query) => $query->where('smazana', true),
            'moznosti',
            'ustajeniMoznosti',
        ]);

        $recentRegistrations = $udalost->prihlasky()
            ->where('smazana', false)
            ->with(['osoba', 'kun'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.udalosti.show', [
            'udalost' => $udalost,
            'recentRegistrations' => $recentRegistrations,
        ]);
    }

    public function store(StoreAdminUdalostRequest $request): RedirectResponse
    {
        $validated = $this->normalizeEventDates($request->validated());
        $validated['aktivni'] = $request->boolean('aktivni', true);
        unset($validated['propozice_pdf_upload'], $validated['vysledky_pdf_upload']);

        if ($request->hasFile('propozice_pdf_upload')) {
            $validated['propozice_pdf'] = $request->file('propozice_pdf_upload')->store('propozice', 'public');
        }
        if ($request->hasFile('vysledky_pdf_upload')) {
            $validated['vysledky_pdf'] = $request->file('vysledky_pdf_upload')->store('vysledky', 'public');
        }

        Udalost::query()->create($validated);

        return redirect()->route('admin.udalosti.index')->with('status', 'udalost-created');
    }

    public function edit(Udalost $udalost): View
    {
        $udalost->load(['moznosti', 'ustajeniMoznosti']);

        return view('admin.udalosti.edit', [
            'udalost' => $udalost,
        ]);
    }

    public function update(UpdateAdminUdalostRequest $request, Udalost $udalost): RedirectResponse
    {
        $validated = $this->normalizeEventDates($request->validated());
        $validated['aktivni'] = $request->boolean('aktivni', false);
        unset($validated['propozice_pdf_upload'], $validated['vysledky_pdf_upload']);

        if ($request->hasFile('propozice_pdf_upload')) {
            $storedPath = $request->file('propozice_pdf_upload')->store('propozice', 'public');
            if ($udalost->propozice_pdf && $udalost->propozice_pdf !== $storedPath) {
                Storage::disk('public')->delete($udalost->propozice_pdf);
            }
            $validated['propozice_pdf'] = $storedPath;
        }
        if ($request->hasFile('vysledky_pdf_upload')) {
            $storedPath = $request->file('vysledky_pdf_upload')->store('vysledky', 'public');
            if ($udalost->vysledky_pdf && $udalost->vysledky_pdf !== $storedPath) {
                Storage::disk('public')->delete($udalost->vysledky_pdf);
            }
            $validated['vysledky_pdf'] = $storedPath;
        }

        $udalost->update($validated);

        return redirect()->route('admin.udalosti.edit', $udalost)->with('status', 'udalost-updated');
    }

    public function destroy(Udalost $udalost): RedirectResponse
    {
        $udalost->delete();

        return redirect()->route('admin.udalosti.index')->with('status', 'udalost-deleted');
    }

    public function storeMoznost(StoreAdminUdalostMoznostRequest $request, Udalost $udalost): RedirectResponse
    {
        $validated = $request->validated();

        $validated['poradi'] = $validated['poradi'] ?? 0;
        $validated['je_administrativni_poplatek'] = $request->boolean('je_administrativni_poplatek', false);
        $udalost->moznosti()->create($validated);

        return redirect()->route('admin.udalosti.edit', $udalost)->with('status', 'moznost-created');
    }

    public function destroyMoznost(Udalost $udalost, UdalostMoznost $moznost): RedirectResponse
    {
        if ($moznost->udalost_id !== $udalost->id) {
            abort(404);
        }

        $moznost->delete();

        return redirect()->route('admin.udalosti.edit', $udalost)->with('status', 'moznost-deleted');
    }

    public function storeUstajeni(StoreAdminUdalostUstajeniRequest $request, Udalost $udalost): RedirectResponse
    {
        $validated = $request->validated();

        $udalost->ustajeniMoznosti()->create($validated);

        return redirect()->route('admin.udalosti.edit', $udalost)->with('status', 'ustajeni-created');
    }

    public function destroyUstajeni(Udalost $udalost, UdalostUstajeni $ustajeni): RedirectResponse
    {
        if ($ustajeni->udalost_id !== $udalost->id) {
            abort(404);
        }

        $ustajeni->delete();

        return redirect()->route('admin.udalosti.edit', $udalost)->with('status', 'ustajeni-deleted');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function normalizeEventDates(array $validated): array
    {
        foreach (['datum_zacatek', 'datum_konec', 'uzavierka_prihlasek'] as $field) {
            if (! empty($validated[$field])) {
                $validated[$field] = CzechDate::toDateString($validated[$field]);
            }
        }

        return $validated;
    }
}
