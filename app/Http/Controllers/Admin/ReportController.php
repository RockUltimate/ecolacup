<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminStartCisloRequest;
use App\Models\Prihlaska;
use App\Models\Udalost;
use App\Services\PrihlaskaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ReportController extends Controller
{
    public function __construct(private readonly PrihlaskaService $prihlaskaService)
    {
    }

    public function prihlasky(Udalost $udalost, Request $request)
    {
        $filters = $this->resolveRegistrationsFilters($request, 'active');
        $prihlasky = $this->registrationsListingQuery($udalost, $filters)
            ->paginate(25)
            ->withQueryString();
        return view('admin.reports.prihlasky', [
            'udalost' => $udalost,
            'filters' => $filters,
            'prihlasky' => $prihlasky,
            'duplicateStartNumbers' => $this->duplicateStartNumbers($prihlasky->getCollection()),
        ]);
    }

    public function updateStartCislo(Udalost $udalost, Prihlaska $prihlaska, UpdateAdminStartCisloRequest $request): RedirectResponse
    {
        if ((int) $prihlaska->udalost_id !== (int) $udalost->id) {
            abort(404);
        }

        $validated = $request->validated();

        $prihlaska->update([
            'start_cislo' => $validated['start_cislo'] ?? null,
        ]);

        return back()->with('status', 'start-cislo-updated');
    }

    public function destroy(Udalost $udalost, Prihlaska $prihlaska): RedirectResponse
    {
        if ((int) $prihlaska->udalost_id !== (int) $udalost->id) {
            abort(404);
        }

        $osobaId = (int) $prihlaska->osoba_id;
        $prihlaska->update(['smazana' => true]);
        $prihlaska->delete();
        $this->prihlaskaService->rebalanceAdminFeeForPersonEvent($udalost, $osobaId);

        return back()->with('status', 'prihlaska-deleted');
    }

    public function smazane(Udalost $udalost, Request $request)
    {
        $filters = $this->resolveRegistrationsFilters($request, 'deleted');
        $prihlasky = $this->registrationsListingQuery($udalost, $filters)
            ->paginate(25)
            ->withQueryString();
        return view('admin.reports.prihlasky', [
            'udalost' => $udalost,
            'showDeleted' => true,
            'filters' => $filters,
            'prihlasky' => $prihlasky,
            'duplicateStartNumbers' => $this->duplicateStartNumbers($prihlasky->getCollection()),
        ]);
    }

    public function startky(Udalost $udalost, Request $request)
    {
        $selectedMoznostId = (int) $request->query('moznost_id', 0);
        $search = trim((string) $request->query('q', ''));
        $needle = $search !== '' ? '%'.$search.'%' : null;

        $moznosti = $udalost->moznosti()
            ->where('je_administrativni_poplatek', false)
            ->when($selectedMoznostId > 0, fn ($query) => $query->where('id', $selectedMoznostId))
            ->paginate(10)
            ->withQueryString();

        $moznosti->setCollection(
            $moznosti->getCollection()->map(function ($moznost) use ($udalost, $needle) {
                $registrations = $this->basePrihlaskyQuery($udalost)
                    ->where('smazana', false)
                    ->whereHas('polozky', fn ($query) => $query->where('moznost_id', $moznost->id))
                    ->when($needle !== null, function ($query) use ($needle) {
                        $query->where(function ($subQuery) use ($needle) {
                            $subQuery
                                ->whereHas('osoba', fn ($osobaQuery) => $osobaQuery
                                    ->where('jmeno', 'like', $needle)
                                    ->orWhere('prijmeni', 'like', $needle))
                                ->orWhereHas('kun', fn ($kunQuery) => $kunQuery
                                    ->where('jmeno', 'like', $needle));
                        });
                    })
                    ->orderBy('start_cislo')
                    ->get();

                return [
                    'moznost' => $moznost,
                    'registrations' => $registrations,
                ];
            })
        );

        return view('admin.reports.startky', [
            'udalost' => $udalost,
            'moznostiSeStartkami' => $moznosti,
            'startkyFilters' => [
                'moznost_id' => $selectedMoznostId,
                'q' => $search,
            ],
            'moznostiOptions' => $udalost->moznosti()->where('je_administrativni_poplatek', false)->get(['id', 'nazev']),
        ]);
    }

    public function ubytovani(Udalost $udalost, Request $request)
    {
        $selectedType = (string) $request->query('typ', 'all');
        if (! in_array($selectedType, ['all', 'ustajeni', 'ubytovani', 'strava', 'ostatni'], true)) {
            $selectedType = 'all';
        }

        $search = trim((string) $request->query('q', ''));
        $needle = $search !== '' ? '%'.$search.'%' : null;

        $options = $udalost->ustajeniMoznosti()
            ->when($selectedType !== 'all', fn ($query) => $query->where('typ', $selectedType))
            ->orderBy('typ')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        $options->setCollection(
            $options->getCollection()->map(function ($option) use ($udalost, $needle) {
                $registrations = $this->basePrihlaskyQuery($udalost)
                    ->where('smazana', false)
                    ->whereHas('ustajeniChoices', fn ($query) => $query->where('ustajeni_id', $option->id))
                    ->when($needle !== null, function ($query) use ($needle) {
                        $query->where(function ($subQuery) use ($needle) {
                            $subQuery
                                ->whereHas('osoba', fn ($osobaQuery) => $osobaQuery
                                    ->where('jmeno', 'like', $needle)
                                    ->orWhere('prijmeni', 'like', $needle))
                                ->orWhereHas('kun', fn ($kunQuery) => $kunQuery
                                    ->where('jmeno', 'like', $needle));
                        });
                    })
                    ->orderBy('start_cislo')
                    ->get();

                return [
                    'option' => $option,
                    'registrations' => $registrations,
                ];
            })
        );
        $byType = $options->getCollection()->groupBy(fn ($item) => $item['option']->typ);

        return view('admin.reports.ubytovani', [
            'udalost' => $udalost,
            'ustajeniByTyp' => $byType,
            'optionsPagination' => $options,
            'ubytovaniFilters' => [
                'typ' => $selectedType,
                'q' => $search,
            ],
        ]);
    }

    public function exporty(Udalost $udalost)
    {
        return view('admin.reports.exporty', [
            'udalost' => $udalost,
        ]);
    }

    public function exportSeznam(Udalost $udalost): Response
    {
        $prihlasky = $this->basePrihlaskyQuery($udalost)
            ->where('smazana', false)
            ->orderBy('start_cislo')
            ->get();

        return $this->xlsResponse(
            view('exports.seznam-prihlasenych', compact('udalost', 'prihlasky'))->render(),
            'Seznam_prihlasenych_'.$udalost->id.'.xls'
        );
    }

    public function exportDiscipliny(Udalost $udalost): Response
    {
        $prihlasky = $this->basePrihlaskyQuery($udalost)
            ->where('smazana', false)
            ->orderBy('start_cislo')
            ->get();
        $moznosti = $udalost->moznosti()->where('je_administrativni_poplatek', false)->orderBy('poradi')->get();
        $ustajeniOptions = $udalost->ustajeniMoznosti()->get();

        return $this->xlsResponse(
            view('exports.discipliny', compact('udalost', 'prihlasky', 'moznosti', 'ustajeniOptions'))->render(),
            'Prihlaseni_a_jejich_discipliny_'.$udalost->id.'.xls'
        );
    }

    public function exportEmaily(Udalost $udalost): Response
    {
        $prihlasky = $this->basePrihlaskyQuery($udalost)
            ->where('smazana', false)
            ->get()
            ->sortBy(fn (Prihlaska $prihlaska) => (string) $prihlaska->user?->email)
            ->values();

        return $this->xlsResponse(
            view('exports.emaily', compact('udalost', 'prihlasky'))->render(),
            'Emaily_prihlasenych_'.$udalost->id.'.xls'
        );
    }

    public function exportKone(Udalost $udalost): Response
    {
        $kone = $udalost->prihlasky()
            ->where('smazana', false)
            ->with('kun')
            ->get()
            ->pluck('kun')
            ->filter()
            ->unique('id')
            ->sortBy('jmeno')
            ->values();

        return $this->xlsResponse(
            view('exports.vet-prejimka', compact('udalost', 'kone'))->render(),
            'Kone_vet_prejimka_'.$udalost->id.'.xls'
        );
    }

    public function exportStartky(Udalost $udalost): Response
    {
        $moznostiSeDisciplinami = $udalost->moznosti()->where('je_administrativni_poplatek', false)->get()->map(function ($moznost) use ($udalost) {
            $registrations = $this->basePrihlaskyQuery($udalost)
                ->where('smazana', false)
                ->whereHas('polozky', fn ($query) => $query->where('moznost_id', $moznost->id))
                ->orderBy('start_cislo')
                ->get();

            return (object) [
                'nazev' => $moznost->nazev,
                'registrations' => $registrations,
            ];
        });

        return $this->xlsResponse(
            view('exports.seznam-startek', compact('udalost', 'moznostiSeDisciplinami'))->render(),
            'Seznam_startek_'.$udalost->id.'.xls'
        );
    }

    public function exportDisciplinyPocty(Udalost $udalost): Response
    {
        $pocty = [];
        foreach ($udalost->moznosti()->where('je_administrativni_poplatek', false)->orderBy('poradi')->get() as $moznost) {
            $pocty[$moznost->nazev] = $moznost->prihlaskyPolozky()
                ->whereHas('prihlaska', fn ($query) => $query->where('udalost_id', $udalost->id)->where('smazana', false))
                ->count();
        }
        $totalStartu = array_sum($pocty);

        return $this->xlsResponse(
            view('exports.discipliny-pocty', compact('udalost', 'pocty', 'totalStartu'))->render(),
            'Seznam_disciplin_a_pocet_startu_'.$udalost->id.'.xls'
        );
    }

    public function exportUstajeni(Udalost $udalost): Response
    {
        $options = $udalost->ustajeniMoznosti()->get();
        $ustajeniByTyp = $options->groupBy('typ')->map(function ($items) use ($udalost) {
            return $items->map(function ($option) use ($udalost) {
                $registrations = $this->basePrihlaskyQuery($udalost)
                    ->where('smazana', false)
                    ->whereHas('ustajeniChoices', fn ($query) => $query->where('ustajeni_id', $option->id))
                    ->orderBy('start_cislo')
                    ->get();

                return (object) [
                    'option' => $option,
                    'registrations' => $registrations,
                ];
            });
        });

        return $this->xlsResponse(
            view('exports.ustajeni', compact('udalost', 'ustajeniByTyp'))->render(),
            'Ustajeni_ubytovani_strava_'.$udalost->id.'.xls'
        );
    }

    public function exportBulkPdf(Udalost $udalost)
    {
        $prihlasky = $this->basePrihlaskyQuery($udalost)
            ->where('smazana', false)
            ->orderBy('start_cislo')
            ->get();

        $tmpDir = storage_path('app/tmp/bulk-pdf-'.$udalost->id.'-'.now()->timestamp);
        File::ensureDirectoryExists($tmpDir);
        $zipPath = $tmpDir.'/prihlasky_'.$udalost->id.'.zip';

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($prihlasky as $prihlaska) {
            $pdfBytes = Pdf::loadView('prihlasky.pdf', ['prihlaska' => $prihlaska])->output();
            $zip->addFromString('prihlaska_'.$prihlaska->id.'.pdf', $pdfBytes);
        }
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function basePrihlaskyQuery(Udalost $udalost)
    {
        return $udalost->prihlasky()
            ->withTrashed()
            ->with([
                'udalost.moznosti',
                'osoba',
                'kun',
                'kunTandem',
                'user',
                'polozky.moznost',
                'ustajeniChoices',
            ]);
    }

    /**
     * @return array{q: string, stav: string}
     */
    private function resolveRegistrationsFilters(Request $request, string $defaultStatus): array
    {
        $status = (string) $request->query('stav', $defaultStatus);
        if (! in_array($status, ['active', 'deleted', 'all'], true)) {
            $status = $defaultStatus;
        }

        return [
            'q' => trim((string) $request->query('q', '')),
            'stav' => $status,
        ];
    }

    public function normalizeStartCisla(Udalost $udalost): RedirectResponse
    {
        $registrations = $udalost->prihlasky()
            ->where('smazana', false)
            ->orderByRaw('CASE WHEN start_cislo IS NULL THEN 1 ELSE 0 END')
            ->orderBy('start_cislo')
            ->orderBy('id')
            ->get();

        $counter = 1;
        foreach ($registrations as $registration) {
            if ((int) $registration->start_cislo !== $counter) {
                $registration->update(['start_cislo' => $counter]);
            }
            $counter++;
        }

        return back()->with('status', 'start-cisla-normalized');
    }

    /**
     * @param  array{q: string, stav: string}  $filters
     */
    private function registrationsListingQuery(Udalost $udalost, array $filters)
    {
        $query = $this->basePrihlaskyQuery($udalost);

        if ($filters['stav'] === 'active') {
            $query->where('smazana', false);
        } elseif ($filters['stav'] === 'deleted') {
            $query->where('smazana', true);
        }

        if ($filters['q'] !== '') {
            $needle = '%'.$filters['q'].'%';
            $query->where(function ($subQuery) use ($needle) {
                $subQuery
                    ->where('start_cislo', 'like', $needle)
                    ->orWhereHas('osoba', fn ($osobaQuery) => $osobaQuery
                        ->where('jmeno', 'like', $needle)
                        ->orWhere('prijmeni', 'like', $needle))
                    ->orWhereHas('kun', fn ($kunQuery) => $kunQuery
                        ->where('jmeno', 'like', $needle))
                    ->orWhereHas('user', fn ($userQuery) => $userQuery
                        ->where('email', 'like', $needle));
            });
        }

        return $query
            ->orderBy('smazana')
            ->orderBy('start_cislo')
            ->orderByDesc('id');
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Prihlaska>  $prihlasky
     * @return array<int, int>
     */
    private function duplicateStartNumbers($prihlasky): array
    {
        return $prihlasky
            ->pluck('start_cislo')
            ->filter(fn ($number) => $number !== null)
            ->countBy()
            ->filter(fn ($count) => $count > 1)
            ->keys()
            ->map(fn ($number) => (int) $number)
            ->values()
            ->all();
    }

    private function xlsResponse(string $html, string $filename): Response
    {
        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
