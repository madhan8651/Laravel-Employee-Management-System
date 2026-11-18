<?php

namespace App\Services;

use App\Mail\BirthdayWishMail;
use App\Models\BirthdayEmailLog;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BirthdayEmailService
{
    public function sendIfEligible(Employee $employee): bool
    {
        // Only send when the employee has a birthday today.
        if (
            !$employee->date_of_birth ||
            $employee->date_of_birth->month !== now()->month ||
            $employee->date_of_birth->day !== now()->day
        ) {
            return false;
        }

        if (empty($employee->email)) {
            Log::warning('Birthday email skipped: employee has no email.', [
                'employee_id' => $employee->id,
            ]);

            return false;
        }

        $year = now()->year;

        // Prevent duplicate birthday emails in the same year.
        $alreadySent = BirthdayEmailLog::query()
            ->where('employee_id', $employee->id)
            ->where('birthday_year', $year)
            ->exists();

        if ($alreadySent) {
            Log::info('Birthday email already sent.', [
                'employee_id' => $employee->id,
                'year' => $year,
            ]);

            return false;
        }

        // Send the birthday email.
        Mail::to($employee->email)
            ->send(new BirthdayWishMail($employee));

        // Record only after the mail send succeeds.
        BirthdayEmailLog::create([
            'employee_id' => $employee->id,
            'birthday_year' => $year,
            'sent_at' => now(),
        ]);

        Log::info('Birthday email sent successfully.', [
            'employee_id' => $employee->id,
            'email' => $employee->email,
            'year' => $year,
        ]);

        return true;
    }
}
