<?php

namespace App\Listeners;

use App\Events\EmployeeCreated;
use App\Services\EmployeeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class SendEmployeeWelcomeEmail implements ShouldQueue
{
    public int $tries = 3;

    public array $backoff = [30, 60, 120];

    public function handle(EmployeeCreated $event): void {
        $status = app(EmployeeService::class)->sendPasswordSetupLink(
            $event->employee
        );

        if ($status !== Password::RESET_LINK_SENT) {
            Log::warning('Employee password setup email was not sent.', [
                'employee_id' => $event->employee->getKey(),
                'email' => $event->employee->email,
                'status' => $status,
            ]);

            throw new \RuntimeException(
                "Password setup email could not be sent: {$status}"
            );
        }
    }
}
