<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        $departments = Department::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'message' => 'Departments retrieved successfully.',
            'data' => $departments,
        ]);
    }
}