<?php

declare(strict_types=1);

namespace Ordinary\UID;

interface UIDInterface
{
    public function value(): string;

    public function timeSeconds(): int;

    public function timeFraction(): int;

    public function timePrecision(): TimePrecision;

    public function uncommon(): string;
}
