<?php

namespace App\Enums;

enum ResultCategory: string
{
    case VERY_GOOD = 'VERY_GOOD';
    case GOOD = 'GOOD';
    case FAIR = 'FAIR';
    case NEEDS_IMPROVEMENT = 'NEEDS_IMPROVEMENT';

    public function label(): string
    {
        return match($this) {
            self::VERY_GOOD => 'Sangat Baik',
            self::GOOD => 'Baik',
            self::FAIR => 'Cukup',
            self::NEEDS_IMPROVEMENT => 'Perlu Pembinaan',
        };
    }

    public static function formatLabel($value): string
    {
        if (empty($value)) {
            return '-';
        }

        if ($value instanceof self) {
            return $value->label();
        }

        $str = (string)$value;
        $enum = self::tryFrom($str);
        if ($enum) {
            return $enum->label();
        }

        $normalized = strtoupper(trim(str_replace(['_', '-'], ' ', $str)));

        return match($normalized) {
            'VERY GOOD', 'VERYGOOD', 'SANGAT BAIK' => 'Sangat Baik',
            'GOOD', 'BAIK' => 'Baik',
            'FAIR', 'CUKUP' => 'Cukup',
            'NEEDS IMPROVEMENT', 'NEEDSIMPROVEMENT', 'BUTUH PERBAIKAN', 'KURANG', 'PERLU PEMBINAAN' => 'Perlu Pembinaan',
            default => ucwords(strtolower($normalized)),
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::VERY_GOOD => 'bg-blue-100 text-blue-800',
            self::GOOD => 'bg-green-100 text-green-800',
            self::FAIR => 'bg-yellow-100 text-yellow-800',
            self::NEEDS_IMPROVEMENT => 'bg-red-100 text-red-800',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::VERY_GOOD => 'Nilai 90-100',
            self::GOOD => 'Nilai 76-89',
            self::FAIR => 'Nilai 61-75',
            self::NEEDS_IMPROVEMENT => 'Nilai <=60',
        };
    }
}
