<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKunRequest;
use App\Http\Requests\UpdateKunRequest;
use App\Models\Kun;
use App\Support\CzechDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KunController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Kun::class);

        return view('kone.index', [
            'kone' => auth()->user()
                ->kone()
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Kun::class);

        return view('kone.create');
    }

    public function store(StoreKunRequest $request): RedirectResponse
    {
        $this->authorize('create', Kun::class);

        $validated = $this->normalizeHealthDates($request->validated());
        $request->user()->kone()->create($validated);

        return redirect()
            ->route('kone.index')
            ->with('status', 'kun-created');
    }

    public function edit(Kun $kun): View
    {
        $this->authorize('update', $kun);

        return view('kone.edit', [
            'kun' => $kun,
        ]);
    }

    public function update(UpdateKunRequest $request, Kun $kun): RedirectResponse
    {
        $this->authorize('update', $kun);

        $kun->update($this->normalizeHealthDates($request->validated()));

        return redirect()
            ->route('kone.index')
            ->with('status', 'kun-updated');
    }

    public function destroy(Kun $kun): RedirectResponse
    {
        $this->authorize('delete', $kun);

        $kun->delete();

        return redirect()
            ->route('kone.index')
            ->with('status', 'kun-deleted');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function normalizeHealthDates(array $validated): array
    {
        foreach (['ehv_datum', 'aie_datum', 'chripka_datum'] as $field) {
            if (! empty($validated[$field])) {
                $validated[$field] = CzechDate::toDateString($validated[$field]);
            }
        }

        return $validated;
    }
}
