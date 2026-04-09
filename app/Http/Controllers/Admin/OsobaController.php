<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOsobaRequest;
use App\Models\Osoba;
use App\Support\CzechDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OsobaController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $osoby = Osoba::query()
            ->with(['user'])
            ->withCount('prihlasky')
            ->when($q !== '', function (Builder $query) use ($q) {
                $needle = '%'.$q.'%';
                $query->where(function (Builder $subQuery) use ($needle) {
                    $subQuery
                        ->where('jmeno', 'like', $needle)
                        ->orWhere('prijmeni', 'like', $needle)
                        ->orWhere('staj', 'like', $needle)
                        ->orWhereHas('user', function (Builder $userQuery) use ($needle) {
                            $userQuery
                                ->where('jmeno', 'like', $needle)
                                ->orWhere('prijmeni', 'like', $needle)
                                ->orWhere('email', 'like', $needle);
                        });
                });
            })
            ->orderBy('prijmeni')
            ->orderBy('jmeno')
            ->paginate(25)
            ->withQueryString();

        return view('admin.osoby.index', [
            'osoby' => $osoby,
            'filters' => ['q' => $q],
        ]);
    }

    public function edit(Osoba $osoba): View
    {
        $osoba->load('user');

        return view('admin.osoby.edit', [
            'osoba' => $osoba,
        ]);
    }

    public function update(UpdateOsobaRequest $request, Osoba $osoba): RedirectResponse
    {
        $gdprOdvolano = (bool) $request->boolean('gdpr_odvolano');
        $gdprSouhlas = $gdprOdvolano ? false : (bool) $request->boolean('gdpr_souhlas', true);

        $osoba->update([
            'jmeno' => $request->string('jmeno')->toString(),
            'prijmeni' => $request->string('prijmeni')->toString(),
            'datum_narozeni' => CzechDate::toDateString($request->input('datum_narozeni')),
            'staj' => $request->string('staj')->toString(),
            'gdpr_souhlas' => $gdprSouhlas,
            'gdpr_odvolano' => $gdprOdvolano,
            'gdpr_souhlas_at' => $gdprSouhlas ? ($osoba->gdpr_souhlas_at ?? now()) : null,
        ]);

        return redirect()->route('admin.osoby.edit', $osoba)->with('status', 'osoba-updated');
    }

    public function destroy(Osoba $osoba): RedirectResponse
    {
        $osoba->delete();

        return redirect()->route('admin.osoby.index')->with('status', 'osoba-deleted');
    }
}
