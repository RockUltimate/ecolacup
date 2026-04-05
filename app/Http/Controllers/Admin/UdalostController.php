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
use Illuminate\Http\RedirectResponse;
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
        $validated = $request->validated();

        $validated['aktivni'] = $request->boolean('aktivni', true);
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
        $validated = $request->validated();

        $validated['aktivni'] = $request->boolean('aktivni', false);
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
}
