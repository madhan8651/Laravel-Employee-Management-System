<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;

/** @property string $status */
class Department extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('dashboard.statistics');
        });

        static::deleted(function () {
            Cache::forget('dashboard.statistics');
        });

    }

    protected $fillable = [
        'name',
        'description',
        'status',
    ];
    public function activityLogs(): MorphMany
{
    return $this->morphMany(ActivityLog::class, 'loggable');
}
    public function users(): BelongsToMany
{
    return $this->belongsToMany(
        User::class,
        'employees',
        'department_id',
        'user_id'
    );
}

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    
public function toggleStatus(Department $department)
{
    $department->status = $department->status === 'active'
        ? 'inactive'
        : 'active';

    $department->save();

    return redirect()
        ->route('admin.departments.index')
        ->with(
            'success',
            "Department {$department->status} successfully."
        );
}
}
