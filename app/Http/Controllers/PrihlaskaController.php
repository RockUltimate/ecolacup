<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrihlaskaRequest;
use App\Http\Requests\UpdatePrihlaskaRequest;
use App\Jobs\SendPrihlaskaEmail;
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
    public function __construct(private readonly PrihlaskaService $prihlaskaService) {}

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

        $isAdmin = (bool) $request->user()?->is_admin;
        $this->ensureNotClosed($udalost, $isAdmin);

        $osobyQuery = $isAdmin ? Osoba::query() : $request->user()->osoby();
        $koneQuery = $isAdmin ? Kun::query() : $request->user()->kone();

        return view('prihlasky.create', [
            'udalost' => $udalost->load(['moznosti', 'ustajeniMoznosti']),
            'osoby' => $osobyQuery->orderBy('prijmeni')->orderBy('jmeno')->get(),
            'kone' => $koneQuery->orderBy('jmeno')->get(),
            'backRoute' => $isAdmin ? route('admin.reports.prihlasky', $udalost) : route('udalosti.show', $udalost),
            'backLabel' => $isAdmin ? 'Zpět na report přihlášek' : 'Zpět na detail akce',
        ]);
    }

    public function store(Udalost $udalost, StorePrihlaskaRequest $request): RedirectResponse
    {
        $this->authorize('create', Prihlaska::class);

        $isAdmin = (bool) $request->user()?->is_admin;
        $this->ensureNotClosed($udalost, $isAdmin);

        $validated = $this->resolvePayload($request->validated(), $udalost, $request);
        $kunTandemId = $validated['kun_tandem_id'] ?? null;

        $prihlaska = Prihlaska::query()->create([
            'udalost_id' => $udalost->id,
            'user_id' => (int) $validated['user_id'],
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
        SendPrihlaskaEmail::dispatch($prihlaska, 'created', true);

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

        $udalost = $prihlaska->udalost()->with(['moznosti', 'ustajeniMoznosti'])->firstOrFail();
        $isAdmin = (bool) $request->user()?->is_admin;
        $this->ensureNotClosed($udalost, $isAdmin);

        $osobyQuery = $isAdmin ? Osoba::query() : $request->user()->osoby();
        $koneQuery = $isAdmin ? Kun::query() : $request->user()->kone();

        return view('prihlasky.edit', [
            'prihlaska' => $prihlaska->load(['polozky', 'ustajeniChoices']),
            'udalost' => $udalost,
            'osoby' => $osobyQuery->orderBy('prijmeni')->orderBy('jmeno')->get(),
            'kone' => $koneQuery->orderBy('jmeno')->get(),
            'backRoute' => $isAdmin ? route('admin.reports.prihlasky', $udalost) : route('prihlasky.index'),
            'backLabel' => $isAdmin ? 'Zpět na report přihlášek' : 'Zpět na přihlášky',
        ]);
    }

    public function update(Prihlaska $prihlaska, UpdatePrihlaskaRequest $request): RedirectResponse
    {
        $this->authorize('update', $prihlaska);

        $udalost = $prihlaska->udalost()->firstOrFail();
        $isAdmin = (bool) $request->user()?->is_admin;
        $this->ensureNotClosed($udalost, $isAdmin);

        $validated = $this->resolvePayload($request->validated(), $udalost, $request, $prihlaska, true);
        $kunTandemId = $validated['kun_tandem_id'] ?? null;

        $prihlaska->update([
            'user_id' => (int) $validated['user_id'],
            'osoba_id' => (int) $validated['osoba_id'],
            'kun_id' => (int) $validated['kun_id'],
            'kun_tandem_id' => $kunTandemId ? (int) $kunTandemId : null,
            'poznamka' => $validated['poznamka'] ?? null,
            'gdpr_souhlas' => true,
        ]);

        $this->prihlaskaService->syncPrihlaska(
            $prihlaska,
            array_map('intval', $validated['moznosti'] ?? []),
            array_map('intval', $validated['ustajeni'] ?? [])
        );
        SendPrihlaskaEmail::dispatch($prihlaska, 'updated', false);

        return redirect()->route('prihlasky.show', $prihlaska)->with('status', 'prihlaska-updated');
    }

    public function destroy(Prihlaska $prihlaska): RedirectResponse
    {
        $this->authorize('delete', $prihlaska);

        $udalost = $prihlaska->udalost()->firstOrFail();
        $osobaId = (int) $prihlaska->osoba_id;
        $prihlaska->update(['smazana' => true]);
        $prihlaska->delete();
        $this->prihlaskaService->rebalanceAdminFeeForPersonEvent($udalost, $osobaId);

        return redirect()->route('prihlasky.index')->with('status', 'prihlaska-deleted');
    }

    public function pdf(Prihlaska $prihlaska)
    {
        $this->authorize('view', $prihlaska);
        $prihlaska->load(['udalost.moznosti', 'osoba', 'kun', 'kunTandem', 'polozky.moznost', 'ustajeniChoices.ustajeni']);

        $pdf = Pdf::loadView('prihlasky.pdf', [
            'prihlaska' => $prihlaska,
        ]);

        return $pdf->download('prihlaska_'.$prihlaska->id.'.pdf');
    }

    public function ajaxOsobaPolozky(Osoba $osoba, Request $request): JsonResponse
    {
        $this->abortIfNotMine($osoba->user_id, $request, true);

        $udalost = Udalost::query()->findOrFail((int) $request->query('udalost'));
        $ignorePrihlaskaId = (int) $request->query('ignore_prihlaska', 0);

        $adminFeeAlreadyChargedQuery = Prihlaska::query()
            ->where('udalost_id', $udalost->id)
            ->where('osoba_id', $osoba->id)
            ->where('smazana', false)
            ->whereHas('polozky', fn ($q) => $q->whereHas('moznost', fn ($m) => $m->where('je_administrativni_poplatek', true)));

        if ($ignorePrihlaskaId > 0) {
            $adminFeeAlreadyChargedQuery->whereKeyNot($ignorePrihlaskaId);
        }

        $adminFeeAlreadyCharged = $adminFeeAlreadyChargedQuery->exists();

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
        $isAdmin = (bool) $request->user()?->is_admin;
        $userId = (int) $request->user()->id;

        if ($isAdmin) {
            $osoba = Osoba::query()->findOrFail((int) $validated['osoba_id']);
            $kun = Kun::query()->findOrFail((int) $validated['kun_id']);
        } else {
            $osoba = Osoba::query()->where('id', $validated['osoba_id'])->where('user_id', $userId)->firstOrFail();
            $kun = Kun::query()->where('id', $validated['kun_id'])->where('user_id', $userId)->firstOrFail();
        }

        $ownerUserId = (int) $osoba->user_id;
        if (! $isAdmin && (int) $kun->user_id !== $ownerUserId) {
            abort(422, 'Vybraný kůň nepatří k vybrané osobě.');
        }

        if (! empty($validated['kun_tandem_id'])) {
            $kunTandemQuery = Kun::query()->where('id', $validated['kun_tandem_id']);
            if (! $isAdmin) {
                $kunTandemQuery->where('user_id', $userId);
            }
            $kunTandem = $kunTandemQuery->firstOrFail();

            if (! $isAdmin && (int) $kunTandem->user_id !== $ownerUserId) {
                abort(422, 'Tandem kůň nepatří k vybrané osobě.');
            }
        }

        $eventMoznostIds = $udalost->moznosti()->pluck('id')->all();
        foreach (($validated['moznosti'] ?? []) as $id) {
            if (! in_array((int) $id, $eventMoznostIds, true)) {
                abort(422, 'Neplatná disciplína pro tuto událost.');
            }
        }

        $selectedDisciplineCount = $udalost->moznosti()
            ->whereIn('id', $validated['moznosti'] ?? [])
            ->where('je_administrativni_poplatek', false)
            ->count();

        if ($selectedDisciplineCount === 0) {
            abort(422, 'Vyberte alespoň jednu disciplínu.');
        }

        $eventUstajeniIds = $udalost->ustajeniMoznosti()->pluck('id')->all();
        foreach (($validated['ustajeni'] ?? []) as $id) {
            if (! in_array((int) $id, $eventUstajeniIds, true)) {
                abort(422, 'Neplatná možnost ustájení pro tuto událost.');
            }
        }

        if ($prihlaska && ! $allowChangeCore) {
            $validated['user_id'] = (int) $prihlaska->user_id;
            $validated['osoba_id'] = $prihlaska->osoba_id;
            $validated['kun_id'] = $prihlaska->kun_id;
        } else {
            $validated['user_id'] = $ownerUserId;
            $validated['osoba_id'] = $osoba->id;
            $validated['kun_id'] = $kun->id;
        }

        return $validated;
    }

    private function ensureNotClosed(Udalost $udalost, bool $ignoreRestrictions = false): void
    {
        if ($ignoreRestrictions) {
            return;
        }

        if ($udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay())) {
            abort(403, 'Uzávěrka přihlášek již proběhla.');
        }

        if ($udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita) {
            abort(403, 'Kapacita akce je naplněna.');
        }
    }

    private function abortIfNotMine(int $ownerId, Request $request, bool $allowAdminBypass = false): void
    {
        if ($allowAdminBypass && (bool) $request->user()?->is_admin) {
            return;
        }

        if ((int) $request->user()->id !== $ownerId) {
            abort(403);
        }
    }
}
