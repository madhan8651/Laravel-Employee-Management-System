<?php

namespace App\Jobs;

use App\Mail\WelcomeEmployeeMail;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Employee $employee
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->employee->email)
            ->send(new WelcomeEmployeeMail($this->employee));
    }
}
