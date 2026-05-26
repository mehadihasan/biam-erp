<?php

namespace App\Support;

class AdminModule
{
    public const SESSION_KEY = 'admin_module';

    public const HOSTEL = 'hostel';

    public const INVENTORY = 'inventory';

    public static function current(): ?string
    {
        $module = session(self::SESSION_KEY);

        return in_array($module, [self::HOSTEL, self::INVENTORY], true) ? $module : null;
    }

    public static function set(?string $module): void
    {
        if ($module === null) {
            session()->forget(self::SESSION_KEY);

            return;
        }

        if (! in_array($module, [self::HOSTEL, self::INVENTORY], true)) {
            return;
        }

        session([self::SESSION_KEY => $module]);
    }

    public static function isHostel(): bool
    {
        return self::current() === self::HOSTEL;
    }

    public static function isInventory(): bool
    {
        return self::current() === self::INVENTORY;
    }

    public static function brandName(): string
    {
        return match (self::current()) {
            self::HOSTEL => 'BHMS',
            self::INVENTORY => 'BIMS',
            default => 'BIAM',
        };
    }
}
