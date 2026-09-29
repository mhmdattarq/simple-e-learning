<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\CourseUser;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * Determine whether the user can view any registrations.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can view the registration detail.
     * - Admin can view all registrations.
     * - Peserta can only view their own registration.
     */
    public function view(User $user, CourseUser $registration): bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        return $user->id === $registration->user_id;
    }

    /**
     * Determine whether the user can verify registration applications.
     */
    public function verify(User $user, CourseUser $registration): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can delete a registration.
     */
    public function delete(User $user, CourseUser $registration): bool
    {
        return $user->role === Role::Admin;
    }
}
