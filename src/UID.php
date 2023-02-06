<?php

declare(strict_types=1);

namespace Ordinary\UID;

use DateTimeImmutable;
use DateTimeInterface;

use const STR_PAD_LEFT;

class UID implements UIDInterface
{
    public const PART_REGEX = <<<'REGEXP'
/^(?<secs>[[:xdigit:]]{8})-(?<prec>[[:xdigit:]])(?<frac>[[:xdigit:]]+)-(?<ubytes>[[:xdigit:]])(?<uncomm>[[:xdigit:]]+)$/
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
            hexdec($matches['ubytes']) * 2 === strlen($matches['uncomm']),
            new UnexpectedValueException('Invalid uncommon in UID - length mismatch'),
        );

        return new self(
            hexdec($matches['secs']),
            hexdec($matches['frac']),
            $precision,
            hex2bin($matches['uncomm']),
        );
    }

    public static function fromDateAndUncommon(DateTimeInterface $dateTime, string $uncommon): self
    {
        return new self(
            $dateTime->getTimestamp(),
            (int) $dateTime->format('u'),
            TimePrecision::Microsecond,
            $uncommon,
        );
    }

    public function __construct(
        private readonly int $timeSeconds,
        private readonly int $timeFraction,
        private readonly TimePrecision $timePrecision,
        private readonly string $uncommon,
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
            dechex(strlen($this->uncommon())) . bin2hex($this->uncommon()),
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

    public function uncommon(): string
    {
        return $this->uncommon;
    }
}
