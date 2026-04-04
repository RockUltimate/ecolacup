<?php

namespace App\Http\Controllers;

use App\Models\Udalost;
use Illuminate\View\View;

class UdalostController extends Controller
{
    public function index(): View
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

        return view('udalosti.index', [
            'upcoming' => $upcoming,
            'archive' => $archive,
        ]);
    }

    public function show(Udalost $udalost): View
    {
        $udalost->load(['moznosti', 'ustajeniMoznosti']);

        return view('udalosti.show', [
            'udalost' => $udalost,
        ]);
    }
}
