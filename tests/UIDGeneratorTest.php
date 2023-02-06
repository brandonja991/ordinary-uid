<?php

declare(strict_types=1);

namespace Ordinary\UID;

use DateTimeImmutable;
use Ordinary\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;
use Random\Engine;
use Random\Randomizer;

class UIDGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $dateTime = new DateTimeImmutable('2005-04-13T07:35:42.234567');
        $frozenClock = new FrozenClock($dateTime);

        $nullRandomizerEngine = new class () implements Engine {
            public function generate(): string
            {
                return "\0";
            }
        };

        $generator = new UIDGenerator($frozenClock, new Randomizer($nullRandomizerEngine));

        $uid = $generator->generate(1);
        $dateFormat = 'Y-m-d\TH:i:s.v';

        self::assertSame('425ccbce-639447-100', $uid->value());
        self::assertSame($dateTime->format($dateFormat), $uid->dateTime()->format($dateFormat));
    }
}
