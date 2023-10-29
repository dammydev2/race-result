<?php

namespace App\Enum;

final class DistanceEnum
{
    public const MEDIUM = 'medium';
    public const LONG = 'long';

    public static function getAllowedValues(): array
    {
        return [
            self::MEDIUM,
            self::LONG
        ];
    }
}