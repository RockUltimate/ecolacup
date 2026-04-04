<?php

namespace App\Policies;

use App\Models\Kun;
use App\Models\User;

class KunPolicy
{
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    public function view(User $user, Kun $kun): bool
    {
        return $kun->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Kun $kun): bool
    {
        return $kun->user_id === $user->id;
    }

    public function delete(User $user, Kun $kun): bool
    {
        return $kun->user_id === $user->id;
    }
}
