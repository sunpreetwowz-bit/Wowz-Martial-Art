<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Upi = 'upi';
    case Paytm = 'paytm';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash (Offline)',
            self::Upi => 'UPI / Google Pay',
            self::Paytm => 'Paytm',
        };
    }

    public function isOffline(): bool
    {
        return $this === self::Cash;
    }
}
