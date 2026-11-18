<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayEmailLog extends Model
{
    protected $fillable = [
        'employee_id',
        'birthday_year',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'birthday_year' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
