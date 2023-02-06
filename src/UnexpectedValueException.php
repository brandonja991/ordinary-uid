<?php

declare(strict_types=1);

namespace Ordinary\UID;

use UnexpectedValueException as PHPUnexpectedValueException;

class UnexpectedValueException extends PHPUnexpectedValueException implements OrdinaryUIDException
{
}
