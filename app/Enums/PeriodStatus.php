<?php

namespace App\Enums;

enum PeriodStatus: string
{
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';
    case ARCHIVED = 'ARCHIVED';
}
