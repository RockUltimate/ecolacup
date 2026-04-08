<?php

namespace App\Http\Controllers;

use App\Jobs\SendPrihlaskaEmail;
use App\Http\Requests\StorePrihlaskaRequest;
use App\Http\Requests\UpdatePrihlaskaRequest;
use App\Models\Kun;
use App\Models\Osoba;
use App\Models\Prihlaska;
use App\Models\Udalost;
use App\Services\PrihlaskaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrihlaskaController extends Controller
{
    public function __construct(private readonly PrihlaskaService $prihlaskaService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Prihlaska::class);

        return view('prihlasky.index', [
            'prihlasky' => $request->user()
                ->prihlasky()
                ->withTrashed()
                ->with(['udalost', 'osoba', 'kun', 'polozky', 'ustajeniChoices'])
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function create(Udalost $udalost, Request $request): View
    {
        $this->authorize('create', Prihlaska::class);
        $this->ensureNotClosed($udalost);

        return view('prihlasky.create', [
            'udalost' => $udalost->load(['moznosti', 'ustajeniMoznosti']),
            'osoby' => $request->user()->osoby()->orderBy('prijmeni')->get(),
            'kone' => $request->user()->kone()->orderBy('jmeno')->get(),
        ]);
    }

    public function store(Udalost $udalost, StorePrihlaskaRequest $request): RedirectResponse
    {
        $this->authorize('create', Prihlaska::class);
        $this->ensureNotClosed($udalost);

        $validated = $this->resolvePayload($request->validated(), $udalost, $request);
        $kunTandemId = $validated['kun_tandem_id'] ?? null;

        $prihlaska = Prihlaska::query()->create([
            'udalost_id' => $udalost->id,
            'user_id' => $request->user()->id,
            'osoba_id' => (int) $validated['osoba_id'],
            'kun_id' => (int) $validated['kun_id'],
            'kun_tandem_id' => $kunTandemId ? (int) $kunTandemId : null,
            'poznamka' => $validated['poznamka'] ?? null,
            'gdpr_souhlas' => true,
            'smazana' => false,
            'cena_celkem' => 0,
        ]);

        $this->prihlaskaService->syncPrihlaska(
            $prihlaska,
            array_map('intval', $validated['moznosti'] ?? []),
            array_map('intval', $validated['ustajeni'] ?? [])
        );
        SendPrihlaskaEmail::dispatch($prihlaska);

        return redirect()->route('prihlasky.show', $prihlaska)->with('status', 'prihlaska-created');
    }

    public function show(Prihlaska $prihlaska): View
    {
        $this->authorize('view', $prihlaska);

        return view('prihlasky.show', [
            'prihlaska' => $prihlaska->load([
                'udalost',
                'osoba',
                'kun',
                'kunTandem',
                'polozky',
                'ustajeniChoices.ustajeni',
            ]),
        ]);
    }

    public function edit(Prihlaska $prihlaska, Request $request): View
    {
        $this->authorize('update', $prihlaska);
        $this->ensureNotClosed($prihlaska->udalost()->firstOrFail());

        return view('prihlasky.edit', [
            'prihlaska' => $prihlaska->load(['polozky', 'ustajeniChoices']),
            'udalost' => $prihlaska->udalost()->with(['moznosti', 'ustajeniMoznosti'])->firstOrFail(),
            'osoby' => $request->user()->osoby()->orderBy('prijmeni')->get(),
            'kone' => $request->user()->kone()->orderBy('jmeno')->get(),
        ]);
    }

    public function update(Prihlaska $prihlaska, UpdatePrihlaskaRequest $request): RedirectResponse
    {
        $this->authorize('update', $prihlaska);
        $udalost = $prihlaska->udalost()->firstOrFail();
        $this->ensureNotClosed($udalost);
        $validated = $this->resolvePayload($request->validated(), $udalost, $request, $prihlaska, false);
        $kunTandemId = $validated['kun_tandem_id'] ?? null;

        $prihlaska->update([
            'kun_tandem_id' => $kunTandemId ? (int) $kunTandemId : null,
            'poznamka' => $validated['poznamka'] ?? null,
            'gdpr_souhlas' => true,
        ]);

        $this->prihlaskaService->syncPrihlaska(
            $prihlaska,
            array_map('intval', $validated['moznosti'] ?? []),
            array_map('intval', $validated['ustajeni'] ?? [])
        );

        return redirect()->route('prihlasky.show', $prihlaska)->with('status', 'prihlaska-updated');
    }

    public function destroy(Prihlaska $prihlaska): RedirectResponse
    {
        $this->authorize('delete', $prihlaska);

        $prihlaska->update(['smazana' => true]);
        $prihlaska->delete();

        return redirect()->route('prihlasky.index')->with('status', 'prihlaska-deleted');
    }

    public function pdf(Prihlaska $prihlaska)
    {
        $this->authorize('view', $prihlaska);
        $prihlaska->load(['udalost', 'osoba', 'kun', 'kunTandem', 'polozky', 'ustajeniChoices.ustajeni']);

        $pdf = Pdf::loadView('prihlasky.pdf', [
            'prihlaska' => $prihlaska,
        ]);

        return $pdf->download('prihlaska_'.$prihlaska->id.'.pdf');
    }

    public function ajaxOsobaPolozky(Osoba $osoba, Request $request): JsonResponse
    {
        $this->abortIfNotMine($osoba->user_id, $request);
        $udalost = Udalost::query()->findOrFail((int) $request->query('udalost'));

        $adminFeeAlreadyCharged = Prihlaska::query()
            ->where('udalost_id', $udalost->id)
            ->where('osoba_id', $osoba->id)
            ->where('smazana', false)
            ->whereHas('polozky', fn ($q) => $q->whereHas('moznost', fn ($m) => $m->where('je_administrativni_poplatek', true)))
            ->exists();

        return response()->json([
            'moznosti' => $udalost->moznosti()->get(),
            'admin_fee_already_charged' => $adminFeeAlreadyCharged,
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function resolvePayload(array $validated, Udalost $udalost, Request $request, ?Prihlaska $prihlaska = null, bool $allowChangeCore = true): array
    {
        $userId = $request->user()->id;

        $osoba = Osoba::query()->where('id', $validated['osoba_id'])->where('user_id', $userId)->firstOrFail();
        $kun = Kun::query()->where('id', $validated['kun_id'])->where('user_id', $userId)->firstOrFail();
        if (! empty($validated['kun_tandem_id'])) {
            Kun::query()->where('id', $validated['kun_tandem_id'])->where('user_id', $userId)->firstOrFail();
        }

        $eventMoznostIds = $udalost->moznosti()->pluck('id')->all();
        foreach (($validated['moznosti'] ?? []) as $id) {
            if (! in_array((int) $id, $eventMoznostIds, true)) {
                abort(422, 'Neplatná disciplína pro tuto událost.');
            }
        }
        $eventUstajeniIds = $udalost->ustajeniMoznosti()->pluck('id')->all();
        foreach (($validated['ustajeni'] ?? []) as $id) {
            if (! in_array((int) $id, $eventUstajeniIds, true)) {
                abort(422, 'Neplatná možnost ustájení pro tuto událost.');
            }
        }

        if ($prihlaska && ! $allowChangeCore) {
            $validated['osoba_id'] = $prihlaska->osoba_id;
            $validated['kun_id'] = $prihlaska->kun_id;
        } else {
            $validated['osoba_id'] = $osoba->id;
            $validated['kun_id'] = $kun->id;
        }

        return $validated;
    }

    private function ensureNotClosed(Udalost $udalost): void
    {
        if ($udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay())) {
            abort(403, 'Uzávěrka přihlášek již proběhla.');
        }

        if ($udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita) {
            abort(403, 'Kapacita akce je naplněna.');
        }
    }

    private function abortIfNotMine(int $ownerId, Request $request): void
    {
        if ((int) $request->user()->id !== $ownerId) {
            abort(403);
        }
    }
}
