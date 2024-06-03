<?php

namespace GS\Contacts;

class ContactTitle
{

    public const MR = 'Mr.';
    public const SIR = 'Sir';
    public const MRS = 'Mrs';
    public const MISS = 'Miss';
    public const MS = 'Ms.';
    public const DR = 'Dr.';
    public const REV = 'Rev.';

    public static function ListAvailable(): array
    {
        return [
            self::MR,
            self::SIR,
            self::MRS,
            self::MISS,
            self::MS,
            self::DR,
            self::REV,
        ];
    }
}
