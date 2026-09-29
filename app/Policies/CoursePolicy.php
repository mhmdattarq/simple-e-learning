<?php

namespace App\Policies;

use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can open or close registration period.
     * Rule PRD: Admin Diklat / Super Admin can configure registration period.
     */
    public function manageRegistration(User $user, ?Course $course = null): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->role === Role::Admin;
    }

    /**
     * Determine whether the user can approve or reject the course plan.
     */
    public function approve(User $user, Course $course): bool
    {
        return $user->role === Role::Admin
            && $course->status === CourseStatus::Submitted;
    }
}
