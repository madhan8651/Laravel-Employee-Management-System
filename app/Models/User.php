<?php

namespace App\Models;

use App\Enums\Role;
use App\Notifications\CustomVerifyEmailNotification;
use App\Notifications\PasswordSetupNotification;
use App\Traits\HasProfilePhoto;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'email',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function employeeIncludingTrashed(): HasOne
    {
        return $this->hasOne(Employee::class)->withTrashed();
    }

    protected function profileValue(string $field): mixed
    {
        $employee = $this->relationLoaded('employee')
            ? $this->getRelation('employee')
            : null;

        if (! $employee) {
            $employee = $this->getRelationValue('employeeIncludingTrashed');
        }

        if ($employee) {
            return $employee->getAttribute($field);
        }

        $profile = $this->getRelationValue('profile');

        return $profile?->getAttribute($field);
    }

    protected function name(): Attribute
    {
        return Attribute::get(
            fn () => $this->profileValue('name')
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::get(
            fn () => $this->profileValue('phone')
        );
    }

    protected function address(): Attribute
    {
        return Attribute::get(
            fn () => $this->profileValue('address')
        );
    }

    protected function profilePhoto(): Attribute
    {
        return Attribute::get(
            fn () => $this->profileValue('profile_photo')
        );
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordSetupNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmailNotification);
    }
}
