# Ordinary UID
Ordinary UID is a simple solution for generating universally unique ids.
## Table of Contents

- [Getting Started](#getting-started)
- [UID Interface](#uid-interface-methods)
  - [`UIDInterface::value()`](#uidinterfacevalue)
  - [`UIDInterface::dateTime()`](#uidinterfacedatetime)
  - [`UIDInterface::timeSeconds()`](#uidinterfacetimeseconds)
  - [`UIDInterface::timeFraction()`](#uidinterfacetimefraction)
  - [`UIDInterface::timePrecision()`](#uidinterfacetimeprecision)
  - [`UIDInterface::custom()`](#uidinterfacecustom)
- [UID Generator](#uid-generator-methods)
  - [`Generator::generate(int $customBytes)`](#generatorgenerateint-custombytes)
  - [`Generator::generateCustom(string $custom)`](#generatorgeneratecustomstring-custom)

## Getting Started
Install using composer.
```shell
composer require ordinary/uid
```

Using the generator

```php
require_once 'vendor/autoload.php';

$randomizer = new \Random\Randomizer(new \Random\Engine\Secure());
$clock = new \Ordinary\Clock\UTCClock();
$generator = new \Ordinary\UID\Generator($clock, $randomizer);

var_dump($generator->generate(4));
var_dump($generator->generateCustom('external-id-1'))
```

## Features
### Universally unique
IDs generated are considered to be universally unique with the assumption that no two machines will generate a UID at the exact same time with the exact same random bytes.
### Time based
The first part is a hexadecimal representation of the timestamp from which it was created. The second part is also a hexadecimal representation of the fractional time of the timestamp from which it was created
### Sortable
With the first two parts being representations of time, UIDs can be easily sorted by creation time, by sorting them by their string value. Additionally, if UID is generated using a custom value for custom bytes, the custom bytes portion will be sortable as well, as long as the custom value would have been sortable.
### Custom time precision
  * Time precision can be customized to use millisecond, microsecond, or nanosecond granularity.
  * Microsecond granularity is default (1,000,000 UIDs per second)
### Custom unique identifiers
The last part contains bytes converted to a hexadecimal (Max: 15). This can be used to add a custom identifier, or to add additional uniqueness to the ID.

## UID Interface Methods
### `UIDInterface::value()`
Get the string value of a UID instance.
### `UIDInterface::dateTime()`
Get a DateTimeImmutable instance of the UID.
### `UIDInterface::timeSeconds()`
Get the timestamp in seconds of the UID.
### `UIDInterface::timeFraction()`
Get the fraction of a second in units determined by the precision.
### `UIDInterface::timePrecision()`
Get the precision used to create the UID.
### `UIDInterface::custom()`
Get the custom bytes of the UID.

## UID Generator Methods
### `Generator::generate(int $customBytes)`
Generate an Ordinary UID with a given number of random bytes.
### `Generator::generateCustom(string $custom)`
Generate an Ordinary UID with the given bytes. `$custom` can be any string as long as the number of bytes is between 1 and 15.