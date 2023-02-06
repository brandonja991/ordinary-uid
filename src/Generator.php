<?php

declare(strict_types=1);

namespace Ordinary\UID;

use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * Service used for generating new UIDs.
 */
class Generator
{
    public function __construct(
        protected readonly ClockInterface $clock,
        protected readonly Randomizer $randomizer,
    ) {
    }

    /** @param int<1,15> $customBytes */
    public function generate(int $customBytes): UID
    {
        assert(
            $customBytes > 0 && $customBytes < 16,
            new UnexpectedValueException('Can not generate UID with ' . $customBytes . ' bytes'),
        );

        return UID::fromDateAndCustom($this->clock->now(), $this->randomizer->getBytes($customBytes));
    }
}
