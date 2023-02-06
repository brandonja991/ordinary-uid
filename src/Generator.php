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

    /**
     * Generate a UID with random custom bytes.
     *
     * @param int<1,15> $customBytes
     */
    public function generate(int $customBytes): UID
    {
        assert(
            $customBytes > 0 && $customBytes < 16,
            new UnexpectedValueException('Can not generate UID with ' . $customBytes . ' bytes'),
        );

        return UID::fromDateAndCustom($this->clock->now(), $this->randomizer->getBytes($customBytes));
    }

    public function generateCustom(string $custom): UID
    {
        assert(
            ($len = strlen($custom)) > 0 && $len < 16,
            new UnexpectedValueException('Custom bytes must be between 1 and 15 characters'),
        );

        return UID::fromDateAndCustom($this->clock->now(), $custom);
    }
}
