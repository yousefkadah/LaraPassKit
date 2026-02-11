<?php

namespace App\Policies;

use App\Models\MediaLibraryAsset;
use App\Models\User;

class MediaLibraryAssetPolicy
{
    /**
     * Determine if the user can view the media asset.
     */
    public function view(User $user, MediaLibraryAsset $asset): bool
    {
        return $asset->owner_user_id === null || $asset->owner_user_id === $user->id;
    }

    /**
     * Determine if the user can delete the media asset.
     */
    public function delete(User $user, MediaLibraryAsset $asset): bool
    {
        return $asset->owner_user_id === $user->id;
    }

    /**
     * Determine if the user can create media assets.
     */
    public function create(User $user): bool
    {
        return true;
    }
}
