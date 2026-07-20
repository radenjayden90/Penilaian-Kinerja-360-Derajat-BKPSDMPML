<?php

namespace App\Enums;

enum AssessmentStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case COMPLETED = 'COMPLETED';
}
