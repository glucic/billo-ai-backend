<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their invoices list
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($invoice->organisation_id) {
            return $invoice->organisation->users()->where('user_id', $user->id)->exists();
        }

        return $invoice->user_id === $user->id;
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($invoice->organisation_id) {
            return $invoice->organisation->users()->where('user_id', $user->id)->where('role', 'admin')->exists();
        }

        return $invoice->user_id === $user->id;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if ($invoice->organisation_id) {
            return $invoice->organisation->users()->where('user_id', $user->id)->where('role', 'admin')->exists();
        }

        return $invoice->user_id === $user->id;
    }
}
