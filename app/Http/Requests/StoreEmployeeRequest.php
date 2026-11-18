<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Employee::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (
            $this->has('employee_code') &&
            \is_string($this->input('employee_code'))
        ) {
            $this->merge([
                'employee_code' => trim($this->input('employee_code')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'employee_code' => [
                'bail',
                'required',
                'string',
                'max:50',
                'regex:/^EMP[0-9]{3}$/i',
                'unique:employees,employee_code',
            ],

            'name' => [
                'bail',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'bail',
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->whereNull('deleted_at'),
                Rule::unique('users', 'email'),
            ],

            'role' => [
                'required',
                Rule::in(['employee', 'admin']),
            ],

            'designation' => [
                'bail',
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'bail',
                'required',
                'string',
                'regex:/^[0-9+\s().-]{7,20}$/',
            ],

            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
                'required_if:status,active',
            ],

            'gender' => [
                'bail',
                'required',
                Rule::in(['male', 'female']),
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'terminated']),
            ],

            'date_of_birth' => [
                'bail',
                'required',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    unset($attribute);

                    if (
                        $value &&
                        Carbon::parse($value)->addYears(18)->isFuture()
                    ) {
                        $fail('The employee must be at least 18 years old.');
                    }
                },
            ],

            'date_of_joining' => [
                'bail',
                'required',
                'date',
                'after_or_equal:date_of_birth',
            ],

            'salary' => [
                'bail',
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'address' => [
                'bail',
                'required',
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

    public function messages(): array
    {
        return [
            'employee_code.required' =>
                'Please enter an employee code.',

            'employee_code.string' =>
                'The employee code must be text.',

            'employee_code.max' =>
                'The employee code cannot exceed 50 characters.',

            'employee_code.regex' =>
                'Employee code must follow the format EMP001, using exactly three digits.',

            'employee_code.unique' =>
                'This employee code is already in use.',

            'name.required' =>
                'Please enter the employee name.',

            'name.string' =>
                'The employee name must be text.',

            'name.max' =>
                'The employee name cannot exceed 255 characters.',

            'email.required' =>
                'Please enter an email address.',

            'email.email' =>
                'Please enter a valid email address.',

            'email.unique' =>
                'This email address is already registered.',

            'phone.regex' =>
                'Please enter a valid phone number.',

            'department_id.exists' =>
                'Please select an existing department.',

            'department_id.required_if' =>
                'Please select a department for an active employee.',

            'gender.required' =>
                'Please select a gender.',

            'gender.in' =>
                'Gender must be either Male or Female.',

            'status.required' =>
                'Please select an employee status.',

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

    public function attributes(): array
    {
        return [
            'employee_code' => 'employee code',
            'department_id' => 'department',
            'date_of_birth' => 'date of birth',
            'date_of_joining' => 'joining date',
            'profile_photo' => 'profile photo',
        ];
    }
}
