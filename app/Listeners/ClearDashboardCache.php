<?php

namespace App\Listeners;

use App\Events\EmployeeCreated;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache
{
    public function handle(EmployeeCreated $event): void
    {
        Cache::forget('dashboard.statistics');
    }
}
