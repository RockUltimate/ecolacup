<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
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
}
