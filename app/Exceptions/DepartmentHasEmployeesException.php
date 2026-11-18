<?php

namespace App\Exceptions;

use RuntimeException;

class DepartmentHasEmployeesException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Cannot delete a department while employees are assigned to it.'
        );
    }
}
