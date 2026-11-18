<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine whether the current user may update this employee.
     */
    public function authorize(): bool
    {
        $employee = $this->route('employee');

        return $employee instanceof Employee
            && $this->user()?->can('update', $employee) === true;
    }

    /**
     * Define validation rules for updating an employee.
     */
    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'employee_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_code')
                    ->ignore($employee->getKey()),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')
                    ->ignore($employee->getKey())
                    ->whereNull('deleted_at'),
            ],

            'designation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
    'required',
    'string',
    'regex:/^[0-9]{10}$/',
],

            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
                'required_if:status,active',
            ],

            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'terminated']),
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    if (
                        $value &&
                        Carbon::parse($value)
                            ->addYears(18)
                            ->isFuture()
                    ) {
                        $fail(
                            'The employee must be at least 18 years old.'
                        );
                    }
                },
            ],

            'date_of_joining' => [
                'nullable',
                'date',
                'after_or_equal:date_of_birth',
            ],

            'salary' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:' . implode(',', config('ems.upload.allowed_image_types')),
                'max:' . config('ems.upload.max_size'),
                'dimensions:max_width=3000,max_height=3000',
            ],
        ];
    }

    /**
     * Provide clearer validation messages.
     */
    public function messages(): array
    {
        return [
            'employee_code.required' =>
                'Please enter an employee code.',

            'employee_code.unique' =>
                'This employee code is already in use.',

            'name.required' =>
                'Please enter the employee name.',

            'email.required' =>
                'Please enter an email address.',

            'email.email' =>
                'Please enter a valid email address.',

            'email.unique' =>
                'This email address belongs to another employee.',

            'phone.regex' =>
                'Please enter a valid phone number.',

            'department_id.exists' =>
                'Please select an existing department.',

            'department_id.required_if' =>
                'Please select a department for an active employee.',

            'status.in' =>
                'Please select a valid employee status.',

            'date_of_birth.before' =>
                'The date of birth must be before today.',

            'date_of_joining.after_or_equal' =>
                'The joining date cannot be before the date of birth.',

            'salary.numeric' =>
                'Salary must be a valid number.',

            'salary.min' =>
                'Salary cannot be negative.',

            'profile_photo.image' =>
                'The profile photo must be an image.',

            'profile_photo.mimes' =>
                'The profile photo must be JPG, JPEG, PNG, or WebP.',

            'profile_photo.max' =>
                'The profile photo must not exceed 2 MB.',

            'profile_photo.dimensions' =>
                'The profile photo dimensions cannot exceed 3000 × 3000 pixels.',
        ];
    }

    /**
     * Use readable field names in validation messages.
     */
    public function attributes(): array
    {
        return [
            'employee_code' => 'employee code',
            'department_id' => 'department',
            'gender' => 'gender',
            'status' => 'employment status',
            'date_of_birth' => 'date of birth',
            'date_of_joining' => 'joining date',
            'profile_photo' => 'profile photo',
        ];
    }
}

