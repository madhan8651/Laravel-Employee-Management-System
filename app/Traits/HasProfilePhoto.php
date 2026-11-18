<?php

namespace App\Traits;

trait HasProfilePhoto
{
    /**
     * Get the protected URL of the profile photo.
     */
    public function getProfilePhotoUrl(): ?string
    {
        if (empty($this->profile_photo)) {
            return null;
        }

        if ($this instanceof \App\Models\Employee) {
            return route('profile-photos.employee', $this->id, false);
        }

        if ($this instanceof \App\Models\User) {
            return route('profile-photos.user', $this->id, false);
        }

        return null;
    }
}
