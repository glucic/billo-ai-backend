<?php

namespace App\Http\Controllers\Traits;

use App\Models\Organisation;
use Illuminate\Auth\AuthenticationException;

trait AuthorizesOrganisation
{
    /**
     * Ensure the given user is a member of the organisation.
     *
     * @throws AuthenticationException
     */
    protected function authorizeUser(int $userId, Organisation $organisation): void
    {
        if (! $organisation || ! $organisation->users()->where('user_id', $userId)->exists()) {
            throw new AuthenticationException('Access denied to organisation.');
        }
    }

    /**
     * Ensure the given user is an admin of the organisation.
     *
     * @throws AuthenticationException
     */
    protected function authorizeAdmin(int $userId, Organisation $organisation): void
    {
        if (! $organisation || ! $organisation->users()->where('user_id', $userId)->where('role', 'admin')->exists()) {
            throw new AuthenticationException('Admin privileges required.');
        }
    }
}
