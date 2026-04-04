<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClenstviCmtRequest;
use App\Http\Requests\UpdateClenstviCmtRequest;
use App\Models\ClenstviCmt;
use App\Models\Osoba;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClenstviCmtController extends Controller
{
    public function index(): View
    {
        return view('clenstvi-cmt.index', [
            'clenstvi' => ClenstviCmt::query()
                ->whereHas('osoba', fn ($query) => $query->where('user_id', auth()->id()))
                ->with('osoba')
                ->orderByDesc('rok')
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('clenstvi-cmt.create', [
            'osoby' => auth()->user()->osoby()->orderBy('prijmeni')->get(),
        ]);
    }

    public function store(StoreClenstviCmtRequest $request): RedirectResponse
    {
        $osoba = Osoba::query()
            ->where('id', (int) $request->input('osoba_id'))
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        ClenstviCmt::query()->create($this->payload($request, $osoba));

        return redirect()->route('clenstvi-cmt.index')->with('status', 'clenstvi-created');
    }

    public function edit(ClenstviCmt $clenstviCmt): View
    {
        $this->abortIfNotMine($clenstviCmt);

        return view('clenstvi-cmt.edit', [
            'clenstvi' => $clenstviCmt->load('osoba'),
            'osoby' => auth()->user()->osoby()->orderBy('prijmeni')->get(),
        ]);
    }

    public function update(UpdateClenstviCmtRequest $request, ClenstviCmt $clenstviCmt): RedirectResponse
    {
        $this->abortIfNotMine($clenstviCmt);

        $osoba = Osoba::query()
            ->where('id', (int) $request->input('osoba_id'))
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $clenstviCmt->update($this->payload($request, $osoba));

        return redirect()->route('clenstvi-cmt.index')->with('status', 'clenstvi-updated');
    }

    public function destroy(ClenstviCmt $clenstviCmt): RedirectResponse
    {
        $this->abortIfNotMine($clenstviCmt);

        $clenstviCmt->delete();

        return redirect()->route('clenstvi-cmt.index')->with('status', 'clenstvi-deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(StoreClenstviCmtRequest|UpdateClenstviCmtRequest $request, Osoba $osoba): array
    {
        $validated = $request->validated();
        $validated['osoba_id'] = $osoba->id;
        $validated['organizace_id'] = (int) ($validated['organizace_id'] ?? 2);
        $validated['aktivni'] = (bool) ($validated['aktivni'] ?? false);
        $validated['souhlas_gdpr'] = (bool) ($validated['souhlas_gdpr'] ?? false);
        $validated['souhlas_email'] = (bool) ($validated['souhlas_email'] ?? false);
        $validated['souhlas_zverejneni'] = (bool) ($validated['souhlas_zverejneni'] ?? false);

        return $validated;
    }

    private function abortIfNotMine(ClenstviCmt $clenstvi): void
    {
        if ((int) $clenstvi->osoba()->value('user_id') !== (int) auth()->id()) {
            abort(403);
        }
    }
}
