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

    /** @param int<1,15> $uncommonBytes */
    public function generate(int $uncommonBytes = 6): UID
    {
        assert(
            $uncommonBytes > 0 && $uncommonBytes < 16,
            new UnexpectedValueException('Can not generate UID with ' . $uncommonBytes . ' bytes'),
        );

        return UID::fromDateAndUncommon($this->clock->now(), $this->randomizer->getBytes($uncommonBytes));
    }
}
