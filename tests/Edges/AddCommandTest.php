<?php

declare(strict_types=1);

namespace Todo\Edges;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @covers \Todo\Edges\AddCommand
 * @covers \Todo\Edges\DisplayHelper
 */
final class AddCommandTest extends KernelTestCase
{
    public function tearDown(): void
    {
        (new FileSystem())->remove(sprintf('%s/../../.data/test/list', __DIR__));
    }

    public function test_it_adds_an_item_to_the_bottom_of_the_list(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:add');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['label' => 'This is a test item on the todo list']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(": [ ] This is a test item on the todo list\n\n", $output);
    }

    public function test_it_fails_when_there_is_no_label_argument_given(): void
    {
        $this->expectException(RuntimeException::class);

        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:add');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]); // Explicitly omitting the label argument
    }

    public function test_it_fails_when_the_label_argument_is_empty(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:add');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['label' => ' ']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(
            'Sorry, you did not provide a label: bin/console todo:add "Type your label here"',
            $output
        );
        $this->assertStringContainsString('### Todo list', $output);
    }

    public function test_it_fails_when_the_label_argument_is_an_array(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:add');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['label' => []]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(
            'Sorry, you did not provide a label: bin/console todo:add "Type your label here"',
            $output
        );
        $this->assertStringContainsString('### Todo list', $output);
    }
}
