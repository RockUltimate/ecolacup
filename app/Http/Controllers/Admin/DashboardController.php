<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kun;
use App\Models\Prihlaska;
use App\Models\Udalost;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'users' => User::query()->count(),
            'horses' => Kun::query()->count(),
            'events' => Udalost::query()->count(),
            'registrations' => Prihlaska::query()->where('smazana', false)->count(),
        ];

        $upcomingEvents = Udalost::query()
            ->where('aktivni', true)
            ->whereDate('datum_zacatek', '>=', now()->startOfDay())
            ->withCount([
                'prihlasky as active_registrations_count' => fn ($query) => $query->where('smazana', false),
            ])
            ->orderBy('datum_zacatek')
            ->limit(6)
            ->get();

        $recentRegistrations = Prihlaska::query()
            ->where('smazana', false)
            ->with(['udalost', 'osoba', 'kun'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'upcomingEvents' => $upcomingEvents,
            'recentRegistrations' => $recentRegistrations,
        ]);
    }
}
