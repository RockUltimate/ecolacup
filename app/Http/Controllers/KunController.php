<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKunRequest;
use App\Http\Requests\UpdateKunRequest;
use App\Models\Kun;
use App\Models\Pleme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KunController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Kun::class);

        return view('kone.index', [
            'kone' => auth()->user()
                ->kone()
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Kun::class);

        return view('kone.create', [
            'plemena' => Pleme::query()->orderBy('poradi')->orderBy('nazev')->get(),
        ]);
    }

    public function store(StoreKunRequest $request): RedirectResponse
    {
        Gate::authorize('create', Kun::class);

        $validated = $request->validated();
        $request->user()->kone()->create($validated);

        return redirect()
            ->route('kone.index')
            ->with('status', 'kun-created');
    }

    public function edit(Kun $kun): View
    {
        Gate::authorize('update', $kun);

        return view('kone.edit', [
            'kun' => $kun,
            'plemena' => Pleme::query()->orderBy('poradi')->orderBy('nazev')->get(),
        ]);
    }

    public function update(UpdateKunRequest $request, Kun $kun): RedirectResponse
    {
        Gate::authorize('update', $kun);

        $kun->update($request->validated());

        return redirect()
            ->route('kone.index')
            ->with('status', 'kun-updated');
    }

    public function destroy(Kun $kun): RedirectResponse
    {
        Gate::authorize('delete', $kun);

        $kun->delete();

        return redirect()
            ->route('kone.index')
            ->with('status', 'kun-deleted');
    }
}
