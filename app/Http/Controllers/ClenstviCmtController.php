<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClenstviCmtRequest;
use App\Http\Requests\UpdateClenstviCmtRequest;
use App\Models\ClenstviCmt;
use App\Models\Osoba;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $payload = $this->payload($request, $osoba);
        $payload['cena'] = $this->resolveMembershipPrice(
            (string) $payload['typ_clenstvi'],
            (int) $payload['rok'],
            (float) $payload['cena']
        );
        if ($this->isNewMember($osoba)) {
            $payload['cena'] += $this->newMemberAdminFee();
        }

        ClenstviCmt::query()->create($payload);

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

        $payload = $this->payload($request, $osoba, $clenstviCmt);
        $payload['cena'] = $this->resolveMembershipPrice(
            (string) $payload['typ_clenstvi'],
            (int) $payload['rok'],
            (float) $payload['cena']
        );
        $clenstviCmt->update($payload);

        return redirect()->route('clenstvi-cmt.index')->with('status', 'clenstvi-updated');
    }

    public function destroy(ClenstviCmt $clenstviCmt): RedirectResponse
    {
        $this->abortIfNotMine($clenstviCmt);

        $clenstviCmt->delete();

        return redirect()->route('clenstvi-cmt.index')->with('status', 'clenstvi-deleted');
    }

    public function renew(ClenstviCmt $clenstviCmt): RedirectResponse
    {
        $this->abortIfNotMine($clenstviCmt);

        $newYear = (int) $clenstviCmt->rok + 1;
        $exists = ClenstviCmt::query()
            ->where('osoba_id', $clenstviCmt->osoba_id)
            ->where('rok', $newYear)
            ->exists();

        if ($exists) {
            return redirect()->route('clenstvi-cmt.index')->with('status', 'clenstvi-renew-exists');
        }

        $renewedPrice = $this->resolveMembershipPrice(
            (string) $clenstviCmt->typ_clenstvi,
            $newYear,
            (float) $clenstviCmt->cena
        );

        $clenstviCmt->update(['aktivni' => false]);
        ClenstviCmt::query()->create([
            'osoba_id' => $clenstviCmt->osoba_id,
            'organizace_id' => $clenstviCmt->organizace_id,
            'evidencni_cislo' => $clenstviCmt->evidencni_cislo,
            'titul' => $clenstviCmt->titul,
            'bydliste' => $clenstviCmt->bydliste,
            'telefon' => $clenstviCmt->telefon,
            'email' => $clenstviCmt->email,
            'nazev_organizace' => $clenstviCmt->nazev_organizace,
            'ico' => $clenstviCmt->ico,
            'typ_clenstvi' => $clenstviCmt->typ_clenstvi,
            'rok' => $newYear,
            'cena' => $renewedPrice,
            'aktivni' => true,
            'zastupce_titul' => $clenstviCmt->zastupce_titul,
            'zastupce_jmeno' => $clenstviCmt->zastupce_jmeno,
            'zastupce_prijmeni' => $clenstviCmt->zastupce_prijmeni,
            'zastupce_rok_narozeni' => $clenstviCmt->zastupce_rok_narozeni,
            'zastupce_vztah' => $clenstviCmt->zastupce_vztah,
            'zastupce_bydliste' => $clenstviCmt->zastupce_bydliste,
            'zastupce_telefon' => $clenstviCmt->zastupce_telefon,
            'zastupce_email' => $clenstviCmt->zastupce_email,
            'sken_prihlaska' => $clenstviCmt->sken_prihlaska,
            'souhlas_gdpr' => $clenstviCmt->souhlas_gdpr,
            'souhlas_email' => $clenstviCmt->souhlas_email,
            'souhlas_zverejneni' => $clenstviCmt->souhlas_zverejneni,
        ]);

        return redirect()->route('clenstvi-cmt.index')->with('status', 'clenstvi-renewed');
    }

    public function ajaxOsobaClenstviData(Osoba $osoba, Request $request): JsonResponse
    {
        if ((int) $osoba->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $latestMembership = $osoba->clenstviCmt()->orderByDesc('rok')->latest('id')->first();
        $isNewMember = ! $osoba->clenstviCmt()->exists();

        return response()->json([
            'osoba' => [
                'id' => $osoba->id,
                'jmeno' => $osoba->jmeno,
                'prijmeni' => $osoba->prijmeni,
                'datum_narozeni' => optional($osoba->datum_narozeni)->format('d.m.Y'),
                'staj' => $osoba->staj,
            ],
            'kontakt' => [
                'telefon' => $latestMembership?->telefon ?: $request->user()->telefon,
                'email' => $latestMembership?->email ?: $request->user()->email,
                'bydliste' => $latestMembership?->bydliste,
            ],
            'posledni_clenstvi' => $latestMembership ? [
                'typ_clenstvi' => $latestMembership->typ_clenstvi,
                'rok' => $latestMembership->rok,
                'cena' => (float) $latestMembership->cena,
                'evidencni_cislo' => $latestMembership->evidencni_cislo,
            ] : null,
            'is_new_member' => $isNewMember,
            'new_member_admin_fee' => $this->newMemberAdminFee(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(
        StoreClenstviCmtRequest|UpdateClenstviCmtRequest $request,
        Osoba $osoba,
        ?ClenstviCmt $existing = null
    ): array
    {
        $validated = $request->validated();
        $validated['osoba_id'] = $osoba->id;
        $validated['organizace_id'] = (int) ($validated['organizace_id'] ?? 2);
        $validated['aktivni'] = (bool) ($validated['aktivni'] ?? false);
        $validated['souhlas_gdpr'] = (bool) ($validated['souhlas_gdpr'] ?? false);
        $validated['souhlas_email'] = (bool) ($validated['souhlas_email'] ?? false);
        $validated['souhlas_zverejneni'] = (bool) ($validated['souhlas_zverejneni'] ?? false);
        unset($validated['sken_prihlaska_upload']);

        if ($request->hasFile('sken_prihlaska_upload')) {
            $storedPath = $request->file('sken_prihlaska_upload')->store('clenstvi', 'public');
            if ($existing?->sken_prihlaska && $existing->sken_prihlaska !== $storedPath) {
                Storage::disk('public')->delete($existing->sken_prihlaska);
            }
            $validated['sken_prihlaska'] = $storedPath;
        } elseif ($existing) {
            $validated['sken_prihlaska'] = $existing->sken_prihlaska;
        }

        return $validated;
    }

    private function abortIfNotMine(ClenstviCmt $clenstvi): void
    {
        if ((int) $clenstvi->osoba()->value('user_id') !== (int) auth()->id()) {
            abort(403);
        }
    }

    private function resolveMembershipPrice(string $membershipType, int $year, float $fallback): float
    {
        $yearlyPrices = (array) config('clenstvi_cmt.yearly_prices.'.$year, []);
        if (array_key_exists($membershipType, $yearlyPrices)) {
            return (float) $yearlyPrices[$membershipType];
        }

        $membershipTypes = (array) config('clenstvi_cmt.membership_types', []);
        $defaultPrice = $membershipTypes[$membershipType]['default_price'] ?? null;
        if ($defaultPrice !== null) {
            return (float) $defaultPrice;
        }

        return $fallback;
    }

    private function isNewMember(Osoba $osoba): bool
    {
        return ! $osoba->clenstviCmt()->exists();
    }

    private function newMemberAdminFee(): float
    {
        return (float) config('clenstvi_cmt.new_member_admin_fee', 100);
    }
}
