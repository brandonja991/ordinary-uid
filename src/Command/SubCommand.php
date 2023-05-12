<?php

declare(strict_types=1);

namespace Ordinary\UID\Command;

use Ordinary\Command\Command;

enum SubCommand: string
{
    case Inspect = 'inspect';
    case Generate = 'generate';

    public function getCommand(Command $parent): Command
    {
        return match ($this) {
            self::Generate => new GenerateUid($parent),
            self::Inspect => new InspectUid($parent),
        };
    }
}
