<?php

declare(strict_types=1);

namespace Ordinary\UID;

use DateTimeImmutable;

interface UIDInterface
{
    public function dateTime(): DateTimeImmutable;

    public function value(): string;

    public function timeSeconds(): int;

    public function timeFraction(): int;

    public function timePrecision(): TimePrecision;

    public function custom(): string;
}
