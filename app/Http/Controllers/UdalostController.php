<?php

namespace App\Http\Controllers;

use App\Models\HomepageMessage;
use App\Models\Udalost;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UdalostController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->startOfDay();

        $upcoming = Udalost::query()
            ->where('aktivni', true)
            ->whereDate('datum_konec', '>=', $today)
            ->orderBy('datum_zacatek')
            ->get();

        $archive = Udalost::query()
            ->where(function ($query) use ($today): void {
                $query->where('aktivni', false)
                    ->orWhereDate('datum_konec', '<', $today);
            })
            ->orderByDesc('datum_zacatek')
            ->get();

        $displayMonth = $this->resolveDisplayMonth($request, $today);
        $calendarStart = $displayMonth->copy()->startOfWeek(CarbonInterface::MONDAY);
        $calendarEnd = $displayMonth->copy()->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY);
        $displayMonthEnd = $displayMonth->copy()->endOfMonth();

        $calendarEvents = Udalost::query()
            ->where('aktivni', true)
            ->whereDate('datum_zacatek', '<=', $calendarEnd)
            ->where(function ($q) use ($calendarStart): void {
                $q->whereDate('datum_konec', '>=', $calendarStart)
                    ->orWhereNull('datum_konec');
            })
            ->orderBy('datum_zacatek')
            ->get();

        $calendarWeeks = collect();
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $week = collect();

            for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                $day = $cursor->copy();

                $eventsForDay = $calendarEvents
                    ->filter(function (Udalost $event) use ($day) {
                        $eventStart = $event->datum_zacatek;
                        $eventEnd = $event->datum_konec ?? $event->datum_zacatek;

                        return $eventStart && $eventEnd
                            && $eventStart->lte($day)
                            && $eventEnd->gte($day);
                    })
                    ->values();

                $week->push([
                    'date' => $day,
                    'in_month' => $day->isSameMonth($displayMonth),
                    'is_today' => $day->isSameDay($today),
                    'events' => $eventsForDay,
                ]);

                $cursor->addDay();
            }

            $calendarWeeks->push($week);
        }

        $calendarMonthEvents = $calendarEvents
            ->filter(function (Udalost $event) use ($displayMonth, $displayMonthEnd) {
                $eventStart = $event->datum_zacatek;
                $eventEnd = $event->datum_konec ?? $event->datum_zacatek;

                return $eventStart && $eventEnd
                    && $eventStart->lte($displayMonthEnd)
                    && $eventEnd->gte($displayMonth);
            })
            ->values();

        $calendarRegistrationCounts = DB::table('prihlasky')
            ->selectRaw('udalost_id, count(*) as registrations_count')
            ->whereIn('udalost_id', $calendarEvents->pluck('id'))
            ->where('smazana', false)
            ->groupBy('udalost_id')
            ->pluck('registrations_count', 'udalost_id');

        $calendarEventMeta = $calendarEvents
            ->mapWithKeys(function (Udalost $event) use ($calendarRegistrationCounts, $today) {
                $registrations = (int) ($calendarRegistrationCounts[$event->id] ?? 0);
                $deadlinePassed = $event->uzavierka_prihlasek?->lt($today) ?? false;
                $capacityReached = $event->kapacita !== null && $registrations >= $event->kapacita;
                $eventEnd = $event->datum_konec ?? $event->datum_zacatek;

                return [
                    $event->id => [
                        'is_closed' => $deadlinePassed || $capacityReached,
                        'is_past' => $eventEnd?->lt($today) ?? false,
                    ],
                ];
            })
            ->all();

        return view('udalosti.index', [
            'upcoming' => $upcoming,
            'archive' => $archive,
            'homepageMessage' => HomepageMessage::singleton(),
            'calendarWeekdays' => ['Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne'],
            'calendarWeeks' => $calendarWeeks,
            'calendarMonthEvents' => $calendarMonthEvents,
            'calendarMonthParam' => $displayMonth->format('Y-m'),
            'calendarMonthLabel' => $this->formatMonthLabel($displayMonth),
            'calendarPrevMonth' => $displayMonth->copy()->subMonth()->format('Y-m'),
            'calendarNextMonth' => $displayMonth->copy()->addMonth()->format('Y-m'),
            'calendarCurrentMonth' => $today->format('Y-m'),
            'calendarEventMeta' => $calendarEventMeta,
        ]);
    }

    public function show(Udalost $udalost): View
    {
        $udalost->load(['moznosti', 'ustajeniMoznosti']);

        return view('udalosti.show', [
            'udalost' => $udalost,
        ]);
    }

    private function resolveDisplayMonth(Request $request, Carbon $today): Carbon
    {
        $month = $request->query('month');

        if (! is_string($month) || ! preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $today->copy()->startOfMonth();
        }

        try {
            return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            return $today->copy()->startOfMonth();
        }
    }

    private function formatMonthLabel(Carbon $month): string
    {
        $months = [
            1 => 'Leden',
            2 => 'Únor',
            3 => 'Březen',
            4 => 'Duben',
            5 => 'Květen',
            6 => 'Červen',
            7 => 'Červenec',
            8 => 'Srpen',
            9 => 'Září',
            10 => 'Říjen',
            11 => 'Listopad',
            12 => 'Prosinec',
        ];

        return ($months[$month->month] ?? $month->format('F')).' '.$month->format('Y');
    }
}
