<?php

declare(strict_types=1);

namespace Ordinary\UID;

use DateTimeImmutable;
use Ordinary\ValueObject\ValueObjectInterface;

interface UIDInterface extends ValueObjectInterface
{
    public function dateTime(): DateTimeImmutable;

    public function externalValue(): string;

    public function timeSeconds(): int;

    public function timeFraction(): int;

    public function timePrecision(): TimePrecision;

    public function custom(): string;
}
