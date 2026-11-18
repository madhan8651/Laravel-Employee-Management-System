<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminEmployeeActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Employee $employee,
        public string $action,
        public array $changedFields = [],
        public ?string $performedBy = null
    ) {}

    public function via(object $_notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $_notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Employee Management Update')
            ->line($this->message())
            ->action(
                'View Employee',
                route('admin.employees.show', $this->employee)
            );
    }

    public function toArray(object $_notifiable): array
    {
        return [
            'title' => 'Employee ' . ucfirst($this->action),
            'message' => $this->message(),
            'employee_id' => $this->employee->getKey(),
            'employee_code' => $this->employee->employee_code,
            'action' => $this->action,
            'changed_fields' => $this->changedFields,
            'performed_by' => $this->performedBy,
            'url' => $this->action === 'deleted'
                ? route('admin.employees.index')
                : route('admin.employees.show', $this->employee),
        ];
    }

    private function message(): string
    {
        $message = match ($this->action) {
            'created' => 'A new employee has been created.',
            'updated' => 'An employee profile has been updated.',
            'status_changed' => 'An employee status has been changed.',
            'deleted' => 'An employee has been deleted.',
            default => 'An employee record has been changed.',
        };

        return "{$message} Employee: {$this->employee->name} ({$this->employee->employee_code}).";
    }
}
