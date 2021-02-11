<?php

declare(strict_types=1);

namespace Todo\Edges;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todo\Domain\Item;
use Todo\Domain\TodoList;

final class AddCommand extends Command
{
    /** @var string (This property cannot be natively typehinted, because the parent class misses the typehint */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'todo:add';
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
        $this->addArgument('label', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $label = trim(is_array($input->getArgument('label'))
            ? ''
            : $input->getArgument('label'));

        if (strlen($label) === 0) {
            $output->writeln('Sorry, you did not provide a label: bin/console todo:add "Type your label here"');
            $this->displayHelper->showTheList($output, $this->todoList);

            return Command::FAILURE;
        }

        $this->todoList->addAnItem(Item::new($label));

        $this->displayHelper->showTheList($output, $this->todoList);

        return Command::SUCCESS;
    }
}
