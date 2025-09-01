<?php

namespace Ihasan\FilamentMailerLite\Enums;

enum SubscriberStatus: string
{
    case ACTIVE = 'active';
    case UNSUBSCRIBED = 'unsubscribed';
    case BOUNCED = 'bounced';
    case JUNK = 'junk';

    public static function options(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::UNSUBSCRIBED->value => 'Unsubscribed',
            self::BOUNCED->value => 'Bounced',
            self::JUNK->value => 'Junk',
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::UNSUBSCRIBED => 'Unsubscribed',
            self::BOUNCED => 'Bounced',
            self::JUNK => 'Junk',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::UNSUBSCRIBED => 'danger',
            self::BOUNCED => 'warning',
            self::JUNK => 'gray',
        };
    }
}
