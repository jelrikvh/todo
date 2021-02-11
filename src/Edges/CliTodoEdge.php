<?php

declare(strict_types=1);

namespace Todo\Edges;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CliTodoEdge extends Command
{
    /** @var string|null (Base class does not set a type, so we can't use the actual typehint) */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'todo:list';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Test output');

        return 0;
    }

    protected function configure(): void
    {
        // Yet to be implemented
    }
}
