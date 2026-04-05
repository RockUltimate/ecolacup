<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminClenstviRequest;
use App\Models\ClenstviCmt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClenstviAdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $year = (int) $request->query('rok', 0);
        $stav = (string) $request->query('stav', 'all');
        if (! in_array($stav, ['all', 'active', 'inactive'], true)) {
            $stav = 'all';
        }

        $memberships = ClenstviCmt::query()
            ->with(['osoba'])
            ->when($year > 0, fn ($query) => $query->where('rok', $year))
            ->when($stav === 'active', fn ($query) => $query->where('aktivni', true))
            ->when($stav === 'inactive', fn ($query) => $query->where('aktivni', false))
            ->when($q !== '', function ($query) use ($q) {
                $needle = '%'.$q.'%';
                $query->where(function ($subQuery) use ($needle) {
                    $subQuery
                        ->where('evidencni_cislo', 'like', $needle)
                        ->orWhere('email', 'like', $needle)
                        ->orWhereHas('osoba', fn ($osobaQuery) => $osobaQuery
                            ->where('jmeno', 'like', $needle)
                            ->orWhere('prijmeni', 'like', $needle));
                });
            })
            ->orderByDesc('rok')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.clenstvi.index', [
            'memberships' => $memberships,
            'filters' => [
                'q' => $q,
                'rok' => $year > 0 ? $year : '',
                'stav' => $stav,
            ],
        ]);
    }

    public function edit(ClenstviCmt $clenstviCmt): View
    {
        return view('admin.clenstvi.edit', [
            'membership' => $clenstviCmt->load('osoba'),
        ]);
    }

    public function update(UpdateAdminClenstviRequest $request, ClenstviCmt $clenstviCmt): RedirectResponse
    {
        $validated = $request->validated();
        $payload = [
            'evidencni_cislo' => $validated['evidencni_cislo'] ?? null,
            'rok' => $validated['rok'],
            'cena' => $validated['cena'],
            'email' => $validated['email'] ?? null,
            'telefon' => $validated['telefon'] ?? null,
            'aktivni' => $request->boolean('aktivni'),
            'souhlas_gdpr' => $request->boolean('souhlas_gdpr'),
            'souhlas_email' => $request->boolean('souhlas_email'),
            'souhlas_zverejneni' => $request->boolean('souhlas_zverejneni'),
        ];

        if ($request->hasFile('sken_prihlaska_upload')) {
            $storedPath = $request->file('sken_prihlaska_upload')->store('clenstvi', 'public');
            if ($clenstviCmt->sken_prihlaska && $clenstviCmt->sken_prihlaska !== $storedPath) {
                Storage::disk('public')->delete($clenstviCmt->sken_prihlaska);
            }
            $payload['sken_prihlaska'] = $storedPath;
        }

        $clenstviCmt->update($payload);

        return redirect()->route('admin.clenstvi.edit', $clenstviCmt)->with('status', 'admin-clenstvi-updated');
    }
}
