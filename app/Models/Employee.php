<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Enums\EmployeeStatus;
use App\Enums\EmployeeGender;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasProfilePhoto;
use Illuminate\Support\Facades\Cache;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasProfilePhoto;

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('dashboard.statistics');
        });

        static::deleted(function () {
            Cache::forget('dashboard.statistics');
        });

        static::forceDeleted(function () {
            Cache::forget('dashboard.statistics');
        });
    }

    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'designation',
        'phone',
        'department_id',
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'date_of_joining',
        'salary',
        'address',
        'profile_photo',
        'status',
    ];
    

    /**
     * Cast model attributes to appropriate PHP types.
     */
    protected function casts(): array
{
    return [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'salary' => 'decimal:2',
        'status' => EmployeeStatus::class,
        'gender' => EmployeeGender::class,
            'address' => 'encrypted',
    ];
}

    /**
     * Accessor: Get the employee's full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fullName = trim(
                    ($this->getAttribute('first_name') ?? '') . ' ' .
                    ($this->getAttribute('last_name') ?? '')
                );

                return $fullName !== '' ? $fullName : $this->name;
            }
        );
    }

    /**
     * Accessor: Calculate the employee's current age.
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_of_birth?->age
        );
    }

    /**
     * Accessor: Calculate the employee's completed years of service.
     */
    protected function yearsOfService(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_of_joining
                ? (int) now()->diffInYears($this->date_of_joining)
                : null
        );
    }

    /**
     * Accessor: Get the employee's service duration.
     */
    protected function serviceDuration(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_of_joining !== null
                ? \Illuminate\Support\Carbon::parse($this->date_of_joining)->diffForHumans(now(), true)
                : null
        );
    }

    /**
     * Accessor: Check whether today is the employee's work anniversary.
     */
    protected function isWorkAnniversary(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_of_joining !== null
                && $this->date_of_joining->month === now()->month
                && $this->date_of_joining->day === now()->day
                && $this->date_of_joining->year < now()->year
        );
    }

    /**
     * Mutator: Normalize the employee's first name when assigning it.
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => ucfirst(strtolower($value))
        );
    }
    
    // Return active employees.
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // Return inactive employees.
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    // Search employee name, email, or employee code.
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('employee_code', 'like', "%{$term}%");
        });
    }


    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)
            ->withTimestamps();
    }
}
