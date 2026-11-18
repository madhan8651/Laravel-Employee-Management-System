<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeCollection extends ResourceCollection
{
    /**
     * Use EmployeeResource for every item.
     */
    public $collects = EmployeeResource::class;

    /**
     * Transform the collection data.
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    /**
     * Add consistent response information.
     */
    public function with(Request $request): array
    {
        return [
            'status' => true,
            'message' => 'Employees retrieved successfully',
        ];
    }
}