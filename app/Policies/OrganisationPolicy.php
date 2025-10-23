<?php

namespace App\Policies;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganisationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view organisations list
    }

    public function view(User $user, Organisation $organisation): bool
    {
        return $organisation->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Organisation $organisation): bool
    {
        return $organisation->users()->where('user_id', $user->id)->where('role', 'admin')->exists();
    }

    public function delete(User $user, Organisation $organisation): bool
    {
        return $organisation->users()->where('user_id', $user->id)->where('role', 'admin')->exists();
    }

    public function join(User $user, Organisation $organisation): bool
    {
        return !$organisation->users()->where('user_id', $user->id)->exists();
    }

    public function leave(User $user, Organisation $organisation): bool
    {
        return $organisation->users()->where('user_id', $user->id)->exists();
    }
}
