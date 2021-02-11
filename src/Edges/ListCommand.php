<?php

declare(strict_types=1);

namespace Todo\Edges;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todo\Domain\TodoList;

final class ListCommand extends Command
{
    /** @var string (This property cannot be natively typehinted, because the parent class misses the typehint */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'todo:list';
    private DisplayHelper $displayHelper;
    private TodoList $todoList;

    public function __construct(TodoList $todoList, DisplayHelper $displayHelper)
    {
        $this->displayHelper = $displayHelper;
        $this->todoList = $todoList;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->displayHelper->showTheList($output, $this->todoList);

        return Command::SUCCESS;
    }
}
