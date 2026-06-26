<?php

namespace App\Policies;

use App\Models\Artifact;
use App\Models\User;

class ArtifactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin();
    }
}
