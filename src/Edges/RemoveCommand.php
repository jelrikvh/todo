<?php

declare(strict_types=1);

namespace Todo\Edges;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todo\Domain\TodoList;

final class RemoveCommand extends Command
{
    /** @var string (This property cannot be natively typehinted, because the parent class misses the typehint */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'todo:remove';
    private DisplayHelper $displayHelper;
    private TodoList $todoList;

    public function __construct(TodoList $todoList, DisplayHelper $displayHelper)
    {
        $this->displayHelper = $displayHelper;
        $this->todoList = $todoList;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('itemNumber', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $itemNumber = is_array($input->getArgument('itemNumber'))
            ? -1
            : $input->getArgument('itemNumber');
        $items = $this->todoList->list();

        if (!array_key_exists($itemNumber, $items)) {
            $output->writeln(sprintf('X There is no item with number %s; ignoring this.', $itemNumber));
            $this->displayHelper->showTheList($output, $this->todoList);

            return Command::FAILURE;
        }

        $this->todoList->removeAnItem($items[$itemNumber]);

        $this->displayHelper->showTheList($output, $this->todoList);

        return Command::SUCCESS;
    }
}
