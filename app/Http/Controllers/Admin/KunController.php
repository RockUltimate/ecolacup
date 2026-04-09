<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateKunRequest;
use App\Models\Kun;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KunController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $koneQuery = Kun::query()->with(['user'])->withCount('prihlasky');
        $this->applyFilters($koneQuery, $q);

        $kone = (clone $koneQuery)
            ->orderBy('jmeno')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $duplicateGroups = $koneQuery
            ->get()
            ->filter(fn (Kun $kun) => filled($kun->jmeno))
            ->groupBy(fn (Kun $kun) => $this->normalizeHorseName($kun->jmeno))
            ->filter(fn ($group) => $group->count() > 1)
            ->sortBy(fn ($group) => Str::lower($group->first()->jmeno))
            ->values();

        $duplicateKeys = $duplicateGroups
            ->map(fn ($group) => $this->normalizeHorseName($group->first()->jmeno))
            ->all();

        return view('admin.kone.index', [
            'kone' => $kone,
            'duplicateGroups' => $duplicateGroups,
            'duplicateKeys' => $duplicateKeys,
            'filters' => ['q' => $q],
        ]);
    }

    public function edit(Kun $kun): View
    {
        $kun->load('user');

        return view('admin.kone.edit', [
            'kun' => $kun,
        ]);
    }

    public function update(UpdateKunRequest $request, Kun $kun): RedirectResponse
    {
        $kun->update($request->validated());

        return redirect()->route('admin.kone.edit', $kun)->with('status', 'kun-updated');
    }

    public function destroy(Kun $kun): RedirectResponse
    {
        $kun->delete();

        return redirect()->route('admin.kone.index')->with('status', 'kun-deleted');
    }

    public function syncDuplicates(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_kun_id' => ['required', 'integer', 'exists:kone,id'],
        ]);

        $sourceHorse = Kun::query()->findOrFail($validated['source_kun_id']);
        $normalizedName = $this->normalizeHorseName($sourceHorse->jmeno);

        DB::transaction(function () use ($normalizedName, $sourceHorse) {
            $duplicateHorses = Kun::query()
                ->whereRaw('LOWER(TRIM(jmeno)) = ?', [$normalizedName])
                ->get();

            $attributes = $this->syncableAttributes($sourceHorse);

            foreach ($duplicateHorses as $horse) {
                $horse->update($attributes);
            }
        });

        return redirect()->route('admin.kone.index')->with('status', 'admin-kun-duplicates-synced');
    }

    protected function applyFilters(Builder $query, string $q): void
    {
        if ($q === '') {
            return;
        }

        $needle = '%'.$q.'%';

        $query->where(function (Builder $subQuery) use ($needle) {
            $subQuery
                ->where('jmeno', 'like', $needle)
                ->orWhere('plemeno_nazev', 'like', $needle)
                ->orWhere('staj', 'like', $needle)
                ->orWhere('cislo_prukazu', 'like', $needle)
                ->orWhereHas('user', function (Builder $userQuery) use ($needle) {
                    $userQuery
                        ->where('jmeno', 'like', $needle)
                        ->orWhere('prijmeni', 'like', $needle)
                        ->orWhere('email', 'like', $needle);
                });
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function syncableAttributes(Kun $kun): array
    {
        return [
            'jmeno' => $kun->jmeno,
            'plemeno_kod' => $kun->plemeno_kod,
            'plemeno_nazev' => $kun->plemeno_nazev,
            'plemeno_vlastni' => $kun->plemeno_vlastni,
            'rok_narozeni' => $kun->rok_narozeni,
            'staj' => $kun->staj,
            'pohlavi' => $kun->pohlavi,
            'cislo_prukazu' => $kun->cislo_prukazu,
            'cislo_hospodarstvi' => $kun->cislo_hospodarstvi,
            'majitel_jmeno_adresa' => $kun->majitel_jmeno_adresa,
        ];
    }

    protected function normalizeHorseName(string $name): string
    {
        return (string) Str::of($name)->trim()->lower();
    }
}
