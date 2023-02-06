<?php

declare(strict_types=1);

namespace Ordinary\UID;

use DateTimeImmutable;
use DateTimeInterface;
use Generator as PHPGenerator;
use Ordinary\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;
use Random\Engine;
use Random\Randomizer;

class UIDGeneratorTest extends TestCase
{
    public function createNullGeneratorWithFrozenClock(DateTimeInterface $dateTime): Generator
    {
        $frozenClock = new FrozenClock($dateTime);

        $nullRandomizerEngine = new class () implements Engine {
            public function generate(): string
            {
                return "\0";
            }
        };

        return new Generator($frozenClock, new Randomizer($nullRandomizerEngine));
    }

    public function customByteCountProvider(): PHPGenerator
    {
        $dateTime = new DateTimeImmutable('2005-04-13T07:35:42.234567');
        $generator = $this->createNullGeneratorWithFrozenClock($dateTime);

        for ($i = 1; $i < 16; $i++) {
            $nullBytes = str_repeat("\0", $i);
            $expected = '425ccbce-639447-' . dechex($i) . bin2hex($nullBytes);

            yield [$generator, $i, $nullBytes, $expected, $dateTime];
        }
    }

    /**
     * @param int<1,15> $customByteLength
     * @dataProvider customByteCountProvider
     */
    public function testGenerate(
        Generator $generator,
        int $customByteLength,
        string $customValue,
        string $expectedUID,
        DateTimeInterface $expectedDate,
    ): void {
        $uid = $generator->generate($customByteLength);
        $dateFormat = 'Y-m-d\TH:i:s.v';

        self::assertSame($expectedUID, $uid->value());
        self::assertSame($customValue, $uid->custom());
        self::assertSame($expectedDate->format($dateFormat), $uid->dateTime()->format($dateFormat));
    }

    public function testGenerateTooManyBytes(): void
    {
        self::expectException(UnexpectedValueException::class);
        $dateTime = new DateTimeImmutable('2005-04-13T07:35:42.234567');
        $generator = $this->createNullGeneratorWithFrozenClock($dateTime);
        /** @psalm-suppress InvalidArgument */
        $generator->generate(16);
    }

    public function testGenerateZeroBytes(): void
    {
        self::expectException(UnexpectedValueException::class);
        $dateTime = new DateTimeImmutable('2005-04-13T07:35:42.234567');
        $generator = $this->createNullGeneratorWithFrozenClock($dateTime);
        /** @psalm-suppress InvalidArgument */
        $generator->generate(0);
    }

    public function testGenerateNegativeBytes(): void
    {
        self::expectException(UnexpectedValueException::class);
        $dateTime = new DateTimeImmutable('2005-04-13T07:35:42.234567');
        $generator = $this->createNullGeneratorWithFrozenClock($dateTime);
        /** @psalm-suppress InvalidArgument */
        $generator->generate(-1);
    }

    public function customValueProvider(): PHPGenerator
    {
        $dateTime = new DateTimeImmutable('2005-04-13T07:35:42.234567');
        $generator = $this->createNullGeneratorWithFrozenClock($dateTime);
        $datePrefix = '425ccbce-639447-';

        for ($i = 1; $i < 16; $i++) {
            yield [
                $generator,
                $nullBytes = str_repeat("\0", $i),
                $datePrefix . dechex($i) . bin2hex($nullBytes),
                $dateTime,
            ];
        }

        yield [$generator, "!@#$", $datePrefix . '421402324', $dateTime];
    }

    /** @dataProvider customValueProvider */
    public function testGenerateCustom(
        Generator $generator,
        string $customValue,
        string $expectedUID,
        DateTimeInterface $expectedDate,
    ): void {
        $uid = $generator->generateCustom($customValue);
        $dateFormat = 'Y-m-d\TH:i:s.v';

        self::assertSame($expectedUID, $uid->value());
        self::assertSame($customValue, $uid->custom());
        self::assertSame($expectedDate->format($dateFormat), $uid->dateTime()->format($dateFormat));
    }
}
