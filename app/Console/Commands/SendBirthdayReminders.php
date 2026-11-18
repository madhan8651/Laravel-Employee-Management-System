<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Services\BirthdayEmailService;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendBirthdayReminders extends Command
{
    protected $signature = 'ems:send-birthday-reminders';

    protected $description = 'Send birthday wishes to employees';

    public function handle(BirthdayEmailService $birthdayEmailService): int
    {
        $today = now();

        $birthdayEmployees = Employee::query()
            ->whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day)
            ->get();

        if ($birthdayEmployees->isEmpty()) {
            $this->info('No birthdays today.');

            return self::SUCCESS;
        }

        foreach ($birthdayEmployees as $employee) {
            try {
                $sent = $birthdayEmailService->sendIfEligible($employee);

                if ($sent) {
                    $this->info("Birthday email sent to {$employee->name}.");
                } else {
                    $this->warn("Birthday email skipped for {$employee->name}.");
                }

            } catch (Throwable $e) {
                $this->error("Failed to send birthday email to {$employee->name}.");

                Log::error('Birthday reminder command failed.', [
                    'employee_id' => $employee->getKey(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return self::SUCCESS;
    }
}
