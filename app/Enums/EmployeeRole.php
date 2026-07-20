<?php

namespace App\Enums;

enum EmployeeRole: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case ADMIN = 'ADMIN';
    case HEAD = 'HEAD';
    case EMPLOYEE = 'EMPLOYEE';
}
