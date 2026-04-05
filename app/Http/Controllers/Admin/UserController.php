<?php

namespace App\Http\Controllers\Admin;

use App\GDPR\UserDataExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly UserDataExport $userDataExport)
    {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $users = User::query()
            ->withCount(['osoby', 'kone', 'prihlasky'])
            ->when($q !== '', function ($query) use ($q) {
                $needle = '%'.$q.'%';
                $query->where(function ($subQuery) use ($needle) {
                    $subQuery
                        ->where('jmeno', 'like', $needle)
                        ->orWhere('prijmeni', 'like', $needle)
                        ->orWhere('email', 'like', $needle);
                });
            })
            ->orderBy('prijmeni')
            ->orderBy('jmeno')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => ['q' => $q],
        ]);
    }

    public function edit(User $user): View
    {
        $user->loadCount(['osoby', 'kone', 'prihlasky']);

        return view('admin.users.edit', ['managedUser' => $user]);
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->update([
            'jmeno' => $validated['jmeno'],
            'prijmeni' => $validated['prijmeni'],
            'email' => $validated['email'],
            'telefon' => $validated['telefon'] ?? null,
            'is_admin' => $request->boolean('is_admin'),
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => $validated['password']]);
        }

        return redirect()->route('admin.users.edit', $user)->with('status', 'admin-user-updated');
    }

    public function gdprExport(User $user): Response
    {
        $csv = $this->userDataExport->toCsv($user);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="gdpr_user_'.$user->id.'.csv"');
    }

    public function purge(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('status', 'admin-user-purge-self-denied');
        }

        DB::transaction(function () use ($user) {
            $user->prihlasky()->withTrashed()->get()->each->forceDelete();
            $user->kone()->withTrashed()->get()->each->forceDelete();
            $user->osoby()->withTrashed()->get()->each->forceDelete();
            $user->forceDelete();
        });

        return redirect()->route('admin.users.index')->with('status', 'admin-user-purged');
    }
}
