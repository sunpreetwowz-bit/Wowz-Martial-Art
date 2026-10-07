<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Assigned = 'assigned';
    case Revoked = 'revoked';

    public function label(): string
    {
        return match ($this) {
            self::Assigned => 'Assigned',
            self::Revoked => 'Revoked',
        };
    }
}
