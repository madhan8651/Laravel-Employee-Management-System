<?php

namespace App\Listeners;

use App\Events\EmployeeCreated;
use Illuminate\Support\Facades\Log;

class LogEmployeeActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeCreated $event): void
    {
        // Simple log to storage/logs/laravel.log
        Log::info('New employee created: '.$event->employee->name.' (ID: '.$event->employee->id.')');

        // Optional: If you have an activity_logs table, you can insert here:
        // \DB::table('activity_logs')->insert([
        //     'description' => 'Created employee: ' . $event->employee->name,
        //     'created_at' => now(),
        // ]);
    }
}
