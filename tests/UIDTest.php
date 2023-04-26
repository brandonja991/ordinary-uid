<?php

declare(strict_types=1);

namespace Ordinary\UID;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use Throwable;
use UnexpectedValueException;

class UIDTest extends TestCase
{
    public static function uidProvider(): Generator
    {
        yield ['00000000-0000-000', UnexpectedValueException::class]; // completely null uid not supported
        yield ['00000000-3000-100', null, true]; // seconds lower bound success
        yield ['ffffffff-3000-100', null]; // seconds upper bound success
        yield ['0000000-3000-100', UnexpectedValueException::class]; // seconds fail too short
        yield ['000000000-3000-100', UnexpectedValueException::class]; // seconds fail too long

        yield ['00000000-3000-100', null, true]; // millitime success (intentionally duplicated for visual redundancy)
        yield ['00000000-300-100', UnexpectedValueException::class]; // millitime fail too short
        yield ['00000000-30000-100', UnexpectedValueException::class]; // millitime fail too long
        yield ['00000000-600000-100', null, true]; // microtime success
        yield ['00000000-60000-100', UnexpectedValueException::class]; // microtime fail too short
        yield ['00000000-6000000-100', UnexpectedValueException::class]; // microtime fail too long
        yield ['00000000-900000000-100', null, true]; // nanotime success
        yield ['00000000-90000000-100', UnexpectedValueException::class]; // nanotime fail too short
        yield ['00000000-9000000000-100', UnexpectedValueException::class]; // nanotime fail too long
    }

    /** @dataProvider uidProvider */
    public function testIsValid(string $uid, ?string $exception): void
    {
        self::assertSame($exception === null, UID::isValid($uid));
    }

    /**
     * @param class-string<Throwable>|null $exception
     * @dataProvider uidProvider
     */
    public function testFromValue(string $uid, ?string $exception, bool $isNull = false): void
    {
        if ($exception !== null) {
            self::expectException($exception);
        }

        $object = UID::fromValue($uid);

        self::assertSame($uid, $object->externalValue());
        self::assertSame($isNull, $object->isNull());
    }

    public function testFromDateAndCustom(): void
    {
        $date = new DateTimeImmutable('2005-04-13T17:18:19.456789');

        $object = UID::fromDateAndCustom($date, '!@#$%^');

        $format = 'Y-m-d\TH:i:s.v';

        self::assertSame($date->format($format), $object->dateTime()->format($format));
        self::assertSame('425d545b-66f855-621402324255e', $object->externalValue());
    }
}
