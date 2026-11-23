<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Department::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:departments,name',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a department name.',
            'name.unique' => 'This department name already exists.',
            'name.max' => 'The department name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'department name',
            'description' => 'department description',
        ];
    }
}
