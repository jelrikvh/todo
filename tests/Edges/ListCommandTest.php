<?php

declare(strict_types=1);

namespace Todo\Edges;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @covers \Todo\Edges\ListCommand
 * @covers \Todo\Edges\DisplayHelper
 */
final class ListCommandTest extends KernelTestCase
{
    public function tearDown(): void
    {
        (new FileSystem())->remove(sprintf('%s/../../.data/test/list', __DIR__));
    }

    public function test_it_lists_the_todo_items(): void
    {
        (new FileSystem())->copy(
            sprintf('%s/todolist-for-tests', __DIR__),
            sprintf('%s/../../.data/test/list', __DIR__)
        );

        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:list');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertSame(
<<<TEXT
### Todo list
 0: [ ] Item 1, unchecked
 1: [ ] Item 2, unchecked
 2: [x] Item 3, checked
 3: [x] Item 4, checked
 4: [ ] Item 5, unchecked

# Logical next steps:
Tick off an item (use the number from the list above): bin/console todo:check 1
Uncheck an item (use the number from the list above): bin/console todo:uncheck 1
Remove an item (use the number from the list above): bin/console todo:remove 1
Move an item up in the list (use the number from the list above): bin/console todo:up 1
Move an item down in the list (use the number from the list above): bin/console todo:down 1
Add an item to the list: bin/console todo:add "Do groceries"

TEXT
        , $output);
    }

    public function test_it_tells_you_you_are_done_for_the_day_when_all_items_are_checked(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:add');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['label' => 'item to check off']);

        $command = $application->find('todo:check');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['itemNumber' => 0]);

        $command = $application->find('todo:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString("You're all done for the day! Yay!", $output);
    }

    public function test_it_says_so_when_the_list_is_empty(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:list');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertSame(
<<<TEXT
### Todo list
You're all done for the day! Yay!

# Logical next steps:
Add an item to the list: bin/console todo:add "Do groceries"

TEXT
        , $output);
    }
}
