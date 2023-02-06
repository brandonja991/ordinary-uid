<?php

declare(strict_types=1);

namespace Ordinary\UID;

use DateTimeImmutable;
use DateTimeInterface;

use const STR_PAD_LEFT;

class UID implements UIDInterface
{
    public const PART_REGEX = <<<'REGEXP'
/^(?<secs>[[:xdigit:]]{8})-(?<prec>[[:xdigit:]])(?<frac>[[:xdigit:]]+)-(?<cbytes>[[:xdigit:]])(?<custom>[[:xdigit:]]+)$/
REGEXP;

    public static function parse(string $uid): self
    {
        assert(
            preg_match(self::PART_REGEX, $uid, $matches),
            new UnexpectedValueException('Invalid UID format: ' . $uid),
        );

        $precision = TimePrecision::fromPrecision(hexdec($matches['prec']));

        assert(
            strlen($matches['frac']) === $precision->padLength(),
            new UnexpectedValueException('Invalid time fraction in UID'),
        );

        assert(
            hexdec($matches['cbytes']) * 2 === strlen($matches['custom']),
            new UnexpectedValueException('Invalid custom part in UID - length mismatch'),
        );

        return new self(
            hexdec($matches['secs']),
            hexdec($matches['frac']),
            $precision,
            hex2bin($matches['custom']),
        );
    }

    public static function fromDateAndCustom(DateTimeInterface $dateTime, string $custom): self
    {
        return new self(
            $dateTime->getTimestamp(),
            (int) $dateTime->format('u'),
            TimePrecision::Microsecond,
            $custom,
        );
    }

    public function __construct(
        private readonly int $timeSeconds,
        private readonly int $timeFraction,
        private readonly TimePrecision $timePrecision,
        private readonly string $custom,
    ) {
    }

    public static function isValid(string $uid): bool
    {
        try {
            self::parse($uid);

            return true;
        } catch (UnexpectedValueException) {
            return false;
        }
    }

    public function value(): string
    {
        return implode('-', [
            str_pad(
                dechex($this->timeSeconds()),
                8,
                '0',
                STR_PAD_LEFT,
            ),
            dechex($this->timePrecision()->precision()) . str_pad(
                dechex($this->timeFraction()),
                $this->timePrecision()->padLength(),
                '0',
                STR_PAD_LEFT,
            ),
            dechex(strlen($this->custom())) . bin2hex($this->custom()),
        ]);
    }

    public function dateTime(): DateTimeImmutable
    {
        $microseconds = match ($this->timePrecision) {
            TimePrecision::Millisecond => $this->timeFraction * 1_000,
            TimePrecision::Microsecond => $this->timeFraction,
            TimePrecision::Nanosecond => intdiv($this->timeFraction, 1_000),
        };

        $dateTime = $this->timeSeconds . ' ' . $microseconds;

        return DateTimeImmutable::createFromFormat('U u', $dateTime)
            ?: throw new UnexpectedValueException('Failed to create datetime from string: ' . $dateTime);
    }

    public function timeSeconds(): int
    {
        return $this->timeSeconds;
    }

    public function timeFraction(): int
    {
        return $this->timeFraction;
    }

    public function timePrecision(): TimePrecision
    {
        return $this->timePrecision;
    }

    public function custom(): string
    {
        return $this->custom;
    }
}
