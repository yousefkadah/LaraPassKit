<?php

namespace App\Policies;

use App\Models\PassTypeSample;
use App\Models\User;

class PassTypeSamplePolicy
{
    /**
     * Determine if the user can view the sample.
     */
    public function view(User $user, PassTypeSample $sample): bool
    {
        return $sample->owner_user_id === null || $sample->owner_user_id === $user->id;
    }

    /**
     * Determine if the user can update the sample.
     */
    public function update(User $user, PassTypeSample $sample): bool
    {
        return $sample->owner_user_id === $user->id;
    }

    /**
     * Determine if the user can delete the sample.
     */
    public function delete(User $user, PassTypeSample $sample): bool
    {
        return $sample->owner_user_id === $user->id;
    }

    /**
     * Determine if the user can create samples.
     */
    public function create(User $user): bool
    {
        return true;
    }
}
