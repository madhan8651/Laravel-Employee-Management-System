<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Employee $employee,
        public array $changedFields,
        public ?string $updatedBy = null
    ) {}

    /**
     * Send through email and database.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Email notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your Employee Profile Has Been Updated')
            ->greeting('Hello '.$this->employee->name.',')
            ->line('Your employee profile has been updated by an administrator.')
            ->line('Updated sections: '.implode(', ', $this->changedFields));

        if ($this->updatedBy) {
            $mail->line('Updated by: '.$this->updatedBy);
        }

        return $mail
            ->action('View My Profile', route('employee.profile.show'))
            ->line('Please contact your administrator if you have questions.');
    }

    /**
     * Database notification data.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Employee Profile Updated',
            'message' => 'Your employee profile has been updated.',
            'employee_id' => $this->employee->id,
            'employee_code' => $this->employee->employee_code,
            'changed_fields' => $this->changedFields,
            'updated_by' => $this->updatedBy,
            'url' => route('employee.profile.show'),
        ];
    }
}
