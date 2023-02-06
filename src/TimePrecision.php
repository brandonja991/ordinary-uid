<?php

declare(strict_types=1);

namespace Ordinary\UID;

enum TimePrecision: string
{
    case Microsecond = 'microsecond';
    case Nanosecond = 'nanosecond';
    case Millisecond = 'millisecond';

    public static function fromPrecision(int $precision): self
    {
        return match ($precision) {
            3 => self::Millisecond,
            6 => self::Microsecond,
            9 => self::Nanosecond,
            default => throw new UnexpectedValueException('Given precision not supported: ' . $precision),
        };
    }

    public function perSecond(): int
    {
        return match ($this) {
            self::Millisecond => 1_000,
            self::Microsecond => 1_000_000,
            self::Nanosecond => 1_000_000_000,
        };
    }

    public function padLength(): int
    {
        return match ($this) {
            self::Millisecond => 3,
            self::Microsecond => 5,
            self::Nanosecond => 8,
        };
    }

    public function precision(): int
    {
        return match ($this) {
            self::Millisecond => 3,
            self::Microsecond => 6,
            self::Nanosecond => 9,
        };
    }
}
