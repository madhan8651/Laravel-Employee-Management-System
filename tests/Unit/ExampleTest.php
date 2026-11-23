<?php

use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    #[Test]
    public function it_sends_a_password_setup_link_to_the_employee_email(): void
    {
        $employee = new Employee;
        $employee->email = 'employee@example.com';

        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(
                ['email' => 'employee@example.com'],
                Mockery::type(Closure::class)
            )
            ->andReturn(Password::RESET_LINK_SENT);

        $service = new EmployeeService;

        $result = $service->sendPasswordSetupLink($employee);

        $this->assertSame(Password::RESET_LINK_SENT, $result);
    }
}
