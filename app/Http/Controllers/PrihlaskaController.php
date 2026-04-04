<?php

namespace App\Http\Controllers;

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
        return view('prihlasky.index', [
            'prihlasky' => $request->user()
                ->prihlasky()
                ->with(['udalost', 'osoba', 'kun', 'polozky', 'ustajeniChoices'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(Udalost $udalost, Request $request): View
    {
        $this->ensureNotClosed($udalost);

        return view('prihlasky.create', [
            'udalost' => $udalost->load(['moznosti', 'ustajeniMoznosti']),
            'osoby' => $request->user()->osoby()->orderBy('prijmeni')->get(),
            'kone' => $request->user()->kone()->orderBy('jmeno')->get(),
        ]);
    }

    public function store(Udalost $udalost, Request $request): RedirectResponse
    {
        $this->ensureNotClosed($udalost);

        $validated = $this->validatePayload($request, $udalost);

        $prihlaska = Prihlaska::query()->create([
            'udalost_id' => $udalost->id,
            'user_id' => $request->user()->id,
            'osoba_id' => (int) $validated['osoba_id'],
            'kun_id' => (int) $validated['kun_id'],
            'kun_tandem_id' => $validated['kun_tandem_id'] ? (int) $validated['kun_tandem_id'] : null,
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

        return redirect()->route('prihlasky.show', $prihlaska)->with('status', 'prihlaska-created');
    }

    public function show(Prihlaska $prihlaska, Request $request): View
    {
        $this->authorizeOwner($prihlaska, $request);

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
        $this->authorizeOwner($prihlaska, $request);
        $this->ensureNotClosed($prihlaska->udalost()->firstOrFail());

        return view('prihlasky.edit', [
            'prihlaska' => $prihlaska->load(['polozky', 'ustajeniChoices']),
            'udalost' => $prihlaska->udalost()->with(['moznosti', 'ustajeniMoznosti'])->firstOrFail(),
            'osoby' => $request->user()->osoby()->orderBy('prijmeni')->get(),
            'kone' => $request->user()->kone()->orderBy('jmeno')->get(),
        ]);
    }

    public function update(Prihlaska $prihlaska, Request $request): RedirectResponse
    {
        $this->authorizeOwner($prihlaska, $request);
        $udalost = $prihlaska->udalost()->firstOrFail();
        $this->ensureNotClosed($udalost);

        $validated = $this->validatePayload($request, $udalost, $prihlaska, false);

        $prihlaska->update([
            'kun_tandem_id' => $validated['kun_tandem_id'] ? (int) $validated['kun_tandem_id'] : null,
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

    public function destroy(Prihlaska $prihlaska, Request $request): RedirectResponse
    {
        $this->authorizeOwner($prihlaska, $request);

        $prihlaska->update(['smazana' => true]);
        $prihlaska->delete();

        return redirect()->route('prihlasky.index')->with('status', 'prihlaska-deleted');
    }

    public function pdf(Prihlaska $prihlaska, Request $request)
    {
        $this->authorizeOwner($prihlaska, $request);
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

    public function ajaxClenstviStatus(Osoba $osoba, Request $request): JsonResponse
    {
        $this->abortIfNotMine($osoba->user_id, $request);
        $clenstvi = $osoba->clenstviCmt()
            ->where('aktivni', true)
            ->orderByDesc('rok')
            ->first();

        if (! $clenstvi) {
            return response()->json([
                'status' => 'none',
                'label' => 'Bez aktivního členství CMT',
            ]);
        }

        return response()->json([
            'status' => 'active',
            'label' => 'Aktivní členství CMT',
            'rok' => $clenstvi->rok,
            'typ_clenstvi' => $clenstvi->typ_clenstvi,
            'evidencni_cislo' => $clenstvi->evidencni_cislo,
            'sken_prihlaska' => $clenstvi->sken_prihlaska,
        ]);
    }

    public function ajaxKunOckovani(Kun $kun, Request $request): JsonResponse
    {
        $this->abortIfNotMine($kun->user_id, $request);

        return response()->json([
            'ockovani' => $kun->ockovaniOk(),
            'ehv_datum' => optional($kun->ehv_datum)->format('d.m.Y'),
            'aie_datum' => optional($kun->aie_datum)->format('d.m.Y'),
            'chripka_datum' => optional($kun->chripka_datum)->format('d.m.Y'),
        ]);
    }

    public function ajaxAdminPoplatek(Udalost $udalost, Request $request): JsonResponse
    {
        $osobaId = (int) $request->query('osoba');
        $osoba = Osoba::query()->where('id', $osobaId)->where('user_id', $request->user()->id)->firstOrFail();

        $alreadyCharged = Prihlaska::query()
            ->where('udalost_id', $udalost->id)
            ->where('osoba_id', $osoba->id)
            ->where('smazana', false)
            ->whereHas('polozky', fn ($q) => $q->whereHas('moznost', fn ($m) => $m->where('je_administrativni_poplatek', true)))
            ->exists();

        return response()->json([
            'already_charged' => $alreadyCharged,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, Udalost $udalost, ?Prihlaska $prihlaska = null, bool $allowChangeCore = true): array
    {
        $userId = $request->user()->id;

        $rules = [
            'osoba_id' => ['required', 'integer', 'exists:osoby,id'],
            'kun_id' => ['required', 'integer', 'exists:kone,id'],
            'kun_tandem_id' => ['nullable', 'integer', 'different:kun_id', 'exists:kone,id'],
            'moznosti' => ['required', 'array', 'min:1'],
            'moznosti.*' => ['integer', 'exists:udalost_moznosti,id'],
            'ustajeni' => ['nullable', 'array'],
            'ustajeni.*' => ['integer', 'exists:udalost_ustajeni,id'],
            'poznamka' => ['nullable', 'string', 'max:2000'],
            'gdpr_souhlas' => ['required', 'accepted'],
        ];

        $validated = $request->validate($rules);

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

    private function authorizeOwner(Prihlaska $prihlaska, Request $request): void
    {
        $this->abortIfNotMine($prihlaska->user_id, $request);
    }

    private function abortIfNotMine(int $ownerId, Request $request): void
    {
        if ((int) $request->user()->id !== $ownerId) {
            abort(403);
        }
    }
}
