<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Udalost;
use Illuminate\View\View;

class StartCislaController extends Controller
{
    public function show(Udalost $udalost): View
    {
        $registrations = $udalost->prihlasky()
            ->where('smazana', false)
            ->with(['osoba', 'kun', 'kunTandem'])
            ->orderByRaw('CASE WHEN start_cislo IS NULL THEN 1 ELSE 0 END')
            ->orderBy('start_cislo')
            ->orderBy('id')
            ->paginate(25);

        return view('admin.start-cisla.show', [
            'udalost' => $udalost,
            'registrations' => $registrations,
        ]);
    }
}
