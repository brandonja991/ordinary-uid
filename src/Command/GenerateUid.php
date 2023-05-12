<?php

declare(strict_types=1);

namespace Ordinary\UID\Command;

use Ordinary\Clock\UTCClock;
use Ordinary\Command\Argument\Option\OptionDefinition;
use Ordinary\Command\Argument\Option\ValueRequirement;
use Ordinary\Command\Command;
use Ordinary\UID\Generator;
use Ordinary\UID\UnexpectedValueException;
use Random\Randomizer;

class GenerateUid extends Command
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
            new OptionDefinition(
                'custom',
                ValueRequirement::Required,
                description: 'Specify the custom bytes to be used in uid',
            ),
            new OptionDefinition(
                'custom-length',
                ValueRequirement::Required,
                description: 'Specify the length (in bytes) of custom bytes generated',
            ),
            new OptionDefinition(
                'hex',
                ValueRequirement::None,
                description: 'Interpret --custom as hex',
            ),
        ];
    }

    public function run(): int
    {
        $generator = new Generator(new UTCClock(), new Randomizer());
        $options = $this->options();

        if ($custom = $options->getString('custom')) {
            try {
                if ($options->exists('hex')) {
                    assert(
                        ctype_xdigit($custom),
                        new UnexpectedValueException('Expecting hex string for --custom'),
                    );
                    $custom = hex2bin($custom);
                }

                $id = $generator->generateCustom($custom);
            } catch (UnexpectedValueException $e) {
                $this->printErr($e->getMessage());

                return 1;
            }

            $this->printOut($id->externalValue());

            return 0;
        }

        $customBytes = $options->getInt('custom-length');
        /** @var int<1,15> $customBytes */
        $customBytes = $customBytes < 1 ? 4 : $customBytes;

        try {
            $id = $generator->generate($customBytes);
        } catch (UnexpectedValueException $e) {
            $this->printErr($e->getMessage());

            return 1;
        }

        $this->printOut($id->externalValue());

        return 0;
    }

    public function showHelp(): void
    {
        $subCommand = SubCommand::Generate;
        $optionSummary = OptionDefinition::makeSummary($this->buildOptions());

        $this->printOut(<<<HELP
Usage: {$this->parent->scriptName()} <options> {$subCommand->value}
$optionSummary

HELP);
    }
}
