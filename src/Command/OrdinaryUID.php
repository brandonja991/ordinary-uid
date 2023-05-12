<?php

declare(strict_types=1);

namespace Ordinary\UID\Command;

use Ordinary\Command\Argument\Option\OptionDefinition;
use Ordinary\Command\Argument\Option\ValueRequirement;
use Ordinary\Command\Command;
use Ordinary\Command\CommandExec;

class OrdinaryUID extends Command
{
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
        if ($subCommand = SubCommand::tryFrom($this->args()[1] ?? '')) {
            $cmdExec = new CommandExec();
            $cmdExec->args = array_slice($this->args(), 1);

            return $cmdExec->execute($subCommand
                ->getCommand($this)
                ->withStreams($this->stdin(), $this->stdout(), $this->stderr()));
        }

        $this->printErr('Invalid sub-command given: ' . ($this->args()[1] ?? 'EMPTY'));

        return 1;
    }

    public function showHelp(): void
    {
        if ($subCommand = SubCommand::tryFrom($this->args()[1] ?? '')) {
            $subCommand
                ->getCommand($this)
                ->withStreams($this->stdin(), $this->stdout(), $this->stderr())
                ->showHelp();

            return;
        }

        $subCommands = implode(', ', array_column(SubCommand::cases(), 'value'));
        $this->printOut(<<<HELP
Usage: {$this->scriptName()} [<options>] [<sub-command> [<sub-command-options>]]
  Sub Commands: $subCommands
  
  Use --help with sub command for sub command details.

HELP);
    }
}
