<?php

if (! function_exists('format_employee_name')) {
    function format_employee_name(?string $name): string
    {
        return $name ? ucwords(strtolower(trim($name))) : 'N/A';
    }
}
