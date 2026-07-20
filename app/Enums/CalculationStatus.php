<?php

namespace App\Enums;

enum CalculationStatus: string
{
    case READY = 'READY';
    case PENDING = 'PENDING';
    case COMPLETE = 'COMPLETE';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match($this) {
            self::READY => 'Siap Dihitung',
            self::PENDING => 'Pending',
            self::COMPLETE => 'Selesai',
            self::FAILED => 'Gagal',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::READY => 'bg-gray-100 text-gray-800',
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::COMPLETE => 'bg-green-100 text-green-800',
            self::FAILED => 'bg-red-100 text-red-800',
        };
    }
}
