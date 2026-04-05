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
        return view('admin.dashboard', [
            'aktivniCount'    => Udalost::where('aktivni', true)->count(),
            'celkemUdalosti'  => Udalost::count(),
            'prihlaskyCount'  => Prihlaska::where('smazana', false)->count(),
            'nadchazejici'    => Udalost::where('aktivni', true)
                                    ->whereDate('datum_konec', '>=', now())
                                    ->orderBy('datum_zacatek')
                                    ->limit(5)
                                    ->get(),
        ]);
    }
}
