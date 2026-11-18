<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:backup-database')
    ->dailyAt('23:00')
    ->withoutOverlapping();
Schedule::command('ems:send-birthday-reminders')->daily();
