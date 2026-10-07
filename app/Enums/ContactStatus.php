<?php

namespace App\Enums;

enum ContactStatus: string
{
    case Unread = 'unread';
    case Read = 'read';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Unread => 'Unread',
            self::Read => 'Read',
            self::Archived => 'Archived',
        };
    }
}
