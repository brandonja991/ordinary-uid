<?php

declare(strict_types=1);

namespace Ordinary\UID\Command;

use DateTimeInterface;
use Ordinary\Command\Argument\Option\OptionDefinition;
use Ordinary\Command\Argument\Option\ValueRequirement;
use Ordinary\Command\Command;
use Ordinary\UID\UID;
use Ordinary\UID\UnexpectedValueException;

use const JSON_PRETTY_PRINT;

class InspectUid extends Command
{
    public function __construct(private readonly Command $parent)
    {
    }

    /** @return OptionDefinition[] */
    public function buildOptions(): array
    {
        return [
            new OptionDefinition(
                'help',
                ValueRequirement::None,
                ['h'],
                'Show this help screen',
            ),
        ];
    }

    public function run(): int
    {
        $uid = $this->args()[1] ?? null;

        if (!isset($uid)) {
            $this->printErr('Argument 2 <uid> required');

            return 1;
        }

        try {
            $obj = UID::fromValue($uid);
        } catch (UnexpectedValueException $e) {
            $this->printErr($e->getMessage());

            return 1;
        }

        $this->printOut(json_encode([
            'Date' => $obj->dateTime()->format(DateTimeInterface::RFC3339_EXTENDED),
            'Time Fraction' => $obj->timeFraction(),
            'Time Precision' => $obj->timePrecision()->name,
            'Custom' => ctype_print($obj->custom())
                ? $obj->custom()
                : '**UNPRINTABLE** \'' . bin2hex($obj->custom()) . '\'',
        ], JSON_PRETTY_PRINT));

        return 0;
    }

    public function showHelp(): void
    {
        $subCommand = SubCommand::Inspect;
        $optionSummary = OptionDefinition::makeSummary($this->buildOptions());

        $this->printOut(<<<HELP
Usage: {$this->parent->scriptName()} <options> {$subCommand->value} <uid>
$optionSummary

HELP);
    }
}
