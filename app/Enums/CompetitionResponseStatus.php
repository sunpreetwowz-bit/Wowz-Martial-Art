<?php

namespace App\Enums;

enum CompetitionResponseStatus: string
{
    case Pending = 'pending';
    case Responded = 'responded';
    case NotRequired = 'not_required';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Responded => 'Responded',
            self::NotRequired => 'Not required',
        };
    }
}
