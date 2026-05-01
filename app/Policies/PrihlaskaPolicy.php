<?php

namespace App\Policies;

use App\Models\Prihlaska;
use App\Models\User;

class PrihlaskaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    public function view(User $user, Prihlaska $prihlaska): bool
    {
        return (bool) $user->is_admin || $prihlaska->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Prihlaska $prihlaska): bool
    {
        return (bool) $user->is_admin || $prihlaska->user_id === $user->id;
    }

    public function delete(User $user, Prihlaska $prihlaska): bool
    {
        return (bool) $user->is_admin || $prihlaska->user_id === $user->id;
    }
}
