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

    public function show(Udalost $udalost): RedirectResponse
    {
        return redirect()->route('admin.udalosti.edit', $udalost);
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

        $udalost = Udalost::query()->create($validated);

        $udalost->moznosti()->create([
            'nazev' => 'Administrativní poplatek',
            'cena' => 100,
            'je_administrativni_poplatek' => true,
            'poradi' => 0,
        ]);

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
        $validated['je_administrativni_poplatek'] = false;

        if ($request->hasFile('foto_path')) {
            $validated['foto_path'] = $request->file('foto_path')->store('disciplines', 'public');
        }
        if ($request->hasFile('pdf_path')) {
            $validated['pdf_path'] = $request->file('pdf_path')->store('disciplines', 'public');
        }

        $udalost->moznosti()->create($validated);

        return $this->redirectToEditTab($udalost, 'discipliny', 'moznost-created');
    }

    public function editMoznost(Udalost $udalost, UdalostMoznost $moznost): View | RedirectResponse
    {
        if ($moznost->udalost_id !== $udalost->id) {
            abort(404);
        }

        if ($moznost->je_administrativni_poplatek) {
            return $this->redirectToEditTab($udalost, 'discipliny', '');
        }

        return view('admin.udalosti.moznost-edit', [
            'udalost' => $udalost,
            'moznost' => $moznost,
        ]);
    }

    public function updateMoznost(Udalost $udalost, UdalostMoznost $moznost, StoreAdminUdalostMoznostRequest $request): RedirectResponse
    {
        if ($moznost->udalost_id !== $udalost->id) {
            abort(404);
        }

        if ($moznost->je_administrativni_poplatek) {
            return $this->redirectToEditTab($udalost, 'discipliny', '');
        }

        $validated = $request->validated();
        $validated['poradi'] = $validated['poradi'] ?? 0;
        $validated['je_administrativni_poplatek'] = false;

        // Handle file uploads
        if ($request->hasFile('foto_path')) {
            $storedPath = $request->file('foto_path')->store('disciplines', 'public');
            if ($moznost->foto_path && $moznost->foto_path !== $storedPath) {
                Storage::disk('public')->delete($moznost->foto_path);
            }
            $validated['foto_path'] = $storedPath;
        }
        if ($request->hasFile('pdf_path')) {
            $storedPath = $request->file('pdf_path')->store('disciplines', 'public');
            if ($moznost->pdf_path && $moznost->pdf_path !== $storedPath) {
                Storage::disk('public')->delete($moznost->pdf_path);
            }
            $validated['pdf_path'] = $storedPath;
        }

        $moznost->update($validated);

        return $this->redirectToEditTab($udalost, 'discipliny', 'moznost-updated');
    }

    public function destroyMoznost(Udalost $udalost, UdalostMoznost $moznost): RedirectResponse
    {
        if ($moznost->udalost_id !== $udalost->id) {
            abort(404);
        }

        if ($moznost->je_administrativni_poplatek) {
            return $this->redirectToEditTab($udalost, 'discipliny', '');
        }

        $moznost->delete();

        return $this->redirectToEditTab($udalost, 'discipliny', 'moznost-deleted');
    }

    public function storeUstajeni(StoreAdminUdalostUstajeniRequest $request, Udalost $udalost): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('foto_path')) {
            $validated['foto_path'] = $request->file('foto_path')->store('services', 'public');
        }
        if ($request->hasFile('pdf_path')) {
            $validated['pdf_path'] = $request->file('pdf_path')->store('services', 'public');
        }

        $udalost->ustajeniMoznosti()->create($validated);

        return $this->redirectToEditTab($udalost, 'sluzby', 'ustajeni-created');
    }

    public function editUstajeni(Udalost $udalost, UdalostUstajeni $ustajeni): View
    {
        if ($ustajeni->udalost_id !== $udalost->id) {
            abort(404);
        }

        return view('admin.udalosti.ustajeni-edit', [
            'udalost' => $udalost,
            'ustajeni' => $ustajeni,
        ]);
    }

    public function updateUstajeni(Udalost $udalost, UdalostUstajeni $ustajeni, StoreAdminUdalostUstajeniRequest $request): RedirectResponse
    {
        if ($ustajeni->udalost_id !== $udalost->id) {
            abort(404);
        }

        $validated = $request->validated();

        // Handle file uploads
        if ($request->hasFile('foto_path')) {
            $storedPath = $request->file('foto_path')->store('services', 'public');
            if ($ustajeni->foto_path && $ustajeni->foto_path !== $storedPath) {
                Storage::disk('public')->delete($ustajeni->foto_path);
            }
            $validated['foto_path'] = $storedPath;
        }
        if ($request->hasFile('pdf_path')) {
            $storedPath = $request->file('pdf_path')->store('services', 'public');
            if ($ustajeni->pdf_path && $ustajeni->pdf_path !== $storedPath) {
                Storage::disk('public')->delete($ustajeni->pdf_path);
            }
            $validated['pdf_path'] = $storedPath;
        }

        $ustajeni->update($validated);

        return $this->redirectToEditTab($udalost, 'sluzby', 'ustajeni-updated');
    }

    public function destroyUstajeni(Udalost $udalost, UdalostUstajeni $ustajeni): RedirectResponse
    {
        if ($ustajeni->udalost_id !== $udalost->id) {
            abort(404);
        }

        $ustajeni->delete();

        return $this->redirectToEditTab($udalost, 'sluzby', 'ustajeni-deleted');
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

    protected function redirectToEditTab(Udalost $udalost, string $tab, string $status): RedirectResponse
    {
        return redirect()
            ->to(route('admin.udalosti.edit', $udalost) . '#' . $tab)
            ->with('status', $status);
    }
}
