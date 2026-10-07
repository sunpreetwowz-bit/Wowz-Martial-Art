<?php

namespace App\Enums;

enum BeltHistorySource: string
{
    case BeltTest = 'belt_test';
    case AdminCorrection = 'admin_correction';
    case Initial = 'initial';

    public function label(): string
    {
        return match ($this) {
            self::BeltTest => 'Belt Test',
            self::AdminCorrection => 'Admin Correction',
            self::Initial => 'Initial Assignment',
        };
    }
}
