<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Services\BirthdayEmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendBirthdayWishJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 60, 120];

    public function __construct(
        public int $employeeId
    ) {}

    public function handle(BirthdayEmailService $birthdayEmailService): void
    {
        $employee = Employee::find($this->employeeId);

        if (!$employee) {
            Log::warning('Birthday email job skipped: employee not found.', [
                'employee_id' => $this->employeeId,
            ]);

            return;
        }

        $birthdayEmailService->sendIfEligible($employee);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Birthday email job permanently failed.', [
            'employee_id' => $this->employeeId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
