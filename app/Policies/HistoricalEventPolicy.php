<?php

namespace App\Policies;

use App\Models\HistoricalEvent;
use App\Models\User;

class HistoricalEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, HistoricalEvent $event): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, HistoricalEvent $event): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, HistoricalEvent $event): bool
    {
        return $user->isAdmin();
    }
}
