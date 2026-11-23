<?php

namespace App\Models;

use App\Traits\HasProfilePhoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasProfilePhoto;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'profile_photo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
