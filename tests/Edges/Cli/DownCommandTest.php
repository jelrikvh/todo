<?php

declare(strict_types=1);

namespace Todo\Edges\Cli;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @covers \Todo\Edges\Cli\DownCommand
 * @covers \Todo\Edges\Cli\DisplayHelper
 */
final class DownCommandTest extends KernelTestCase
{
    public function tearDown(): void
    {
        (new FileSystem())->remove(sprintf('%s/../../.data/test/list', __DIR__));
    }

    public function test_it_moves_an_item_down(): void
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
        $this->assertStringContainsString(
            " 1: [ ] Item 2, unchecked\n 2: [x] Item 3, checked",
            $output,
            "We expected items 1 and 2 to be in the correct order when this test starts; they aren't."
        );

        $command = $application->find('todo:down');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['itemNumber' => 1]);

        $output = $commandTester->getDisplay();
        $this->assertSame(
            <<<TEXT
### Todo list
 0: [ ] Item 1, unchecked
 1: [x] Item 3, checked
 2: [ ] Item 2, unchecked
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

    public function test_it_does_not_fail_when_the_item_is_already_at_the_bottom_of_the_list(): void
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
        $this->assertStringContainsString(
            '4: [ ] Item 5, unchecked',
            $output,
            "The last item was expected to be at the top already when the test starts; it isn't."
        );

        $command = $application->find('todo:down');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['itemNumber' => 4]);

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

    public function test_it_lets_you_know_when_you_entered_a_number_that_is_not_in_the_list(): void
    {
        (new FileSystem())->copy(
            sprintf('%s/todolist-for-tests', __DIR__),
            sprintf('%s/../../.data/test/list', __DIR__)
        );

        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:down');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['itemNumber' => 5]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('X There is no item with number 5; ignoring this.', $output);
        $this->assertStringContainsString('### Todo list', $output);
    }

    public function test_it_lets_you_know_when_you_somehow_supplied_an_array_as_the_item_number(): void
    {
        (new FileSystem())->copy(
            sprintf('%s/todolist-for-tests', __DIR__),
            sprintf('%s/../../.data/test/list', __DIR__)
        );

        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:down');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['itemNumber' => []]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('X There is no item with number -1; ignoring this.', $output);
        $this->assertStringContainsString('### Todo list', $output);
    }

    public function test_it_lets_you_know_when_you_somehow_supplied_null_as_the_item_number(): void
    {
        (new FileSystem())->copy(
            sprintf('%s/todolist-for-tests', __DIR__),
            sprintf('%s/../../.data/test/list', __DIR__)
        );

        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:down');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['itemNumber' => null]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('X There is no item with number -1; ignoring this.', $output);
        $this->assertStringContainsString('### Todo list', $output);
    }
}
