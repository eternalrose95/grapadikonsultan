<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ExecutiveTeam;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExecutiveTeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_executive::team');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExecutiveTeam $executiveTeam): bool
    {
        return $user->can('view_executive::team');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_executive::team');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExecutiveTeam $executiveTeam): bool
    {
        return $user->can('update_executive::team');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExecutiveTeam $executiveTeam): bool
    {
        return $user->can('delete_executive::team');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_executive::team');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ExecutiveTeam $executiveTeam): bool
    {
        return $user->can('force_delete_executive::team');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_executive::team');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ExecutiveTeam $executiveTeam): bool
    {
        return $user->can('restore_executive::team');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_executive::team');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ExecutiveTeam $executiveTeam): bool
    {
        return $user->can('replicate_executive::team');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_executive::team');
    }
}
