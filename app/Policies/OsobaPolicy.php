<?php

namespace App\Policies;

use App\Models\Osoba;
use App\Models\User;

class OsobaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    public function view(User $user, Osoba $osoba): bool
    {
        return $osoba->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Osoba $osoba): bool
    {
        return $osoba->user_id === $user->id;
    }

    public function delete(User $user, Osoba $osoba): bool
    {
        return $osoba->user_id === $user->id;
    }
}
