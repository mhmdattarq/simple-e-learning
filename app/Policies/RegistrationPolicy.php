<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\CourseUser;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * Determine whether the user can view any registrations.
     * Admin, Verifikator, Pimpinan can view the registration list.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::Verifikator, Role::Pimpinan], true);
    }

    /**
     * Determine whether the user can view the registration detail.
     * - Internal roles can view all registrations.
     * - Peserta can only view their own registration.
     */
    public function view(User $user, CourseUser $registration): bool
    {
        if (in_array($user->role, [Role::Admin, Role::Verifikator, Role::Pimpinan], true)) {
            return true;
        }

        return $user->id === $registration->user_id;
    }

    /**
     * Determine whether the user can verify registration applications.
     */
    public function verify(User $user, CourseUser $registration): bool
    {
        return in_array($user->role, [Role::Admin, Role::Verifikator], true);
    }

    /**
     * Determine whether the user can delete a registration.
     */
    public function delete(User $user, CourseUser $registration): bool
    {
        return $user->role === Role::Admin;
    }
}
