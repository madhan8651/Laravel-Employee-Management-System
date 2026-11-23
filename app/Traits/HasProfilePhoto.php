<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\User;

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

        if ($this instanceof Employee) {
            return route('profile-photos.employee', $this->id, false);
        }

        if ($this instanceof User) {
            return route('profile-photos.user', $this->id, false);
        }

        return null;
    }
}
