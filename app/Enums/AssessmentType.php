<?php

namespace App\Enums;

enum AssessmentType: string
{
    case SUPERIOR = 'SUPERIOR';
    case PEER = 'PEER';
    case SUBORDINATE = 'SUBORDINATE';
}
