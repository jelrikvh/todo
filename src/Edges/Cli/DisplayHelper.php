<?php

declare(strict_types=1);

namespace Todo\Edges\Cli;

use Symfony\Component\Console\Output\OutputInterface;
use Todo\Domain\TodoList;

final class DisplayHelper
{
    public function showTheList(OutputInterface $output, TodoList $todoList): void
    {
        $output->writeln('### Todo list');

        if ($todoList->isAllDone()) {
            $output->writeln("You're all done for the day! Yay!");
        }

        foreach ($todoList->list() as $number => $item) {
            $output->writeln(sprintf(
                '% 2d: [%s] %s',
                $number,
                $item->isChecked() ? 'x' : ' ',
                $item->label()
            ));
        }

        $output->writeln('');
        $output->writeln('# Logical next steps:');

        if (!$todoList->isEmpty()) {
            $output->writeln('Tick off an item (use the number from the list above): bin/console todo:check 1');
            $output->writeln('Uncheck an item (use the number from the list above): bin/console todo:uncheck 1');
            $output->writeln('Remove an item (use the number from the list above): bin/console todo:remove 1');
            $output->writeln('Move an item up in the list (use the number from the list above): bin/console todo:up 1');
            $output->writeln(
                'Move an item down in the list (use the number from the list above): bin/console todo:down 1'
            );
        }

        $output->writeln('Add an item to the list: bin/console todo:add "Do groceries"');
    }
}
