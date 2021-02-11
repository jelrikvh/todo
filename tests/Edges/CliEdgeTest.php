<?php declare(strict_types=1);

namespace Todo\Edges;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Todo\Edges\CliTodoEdge
 */
final class CliEdgeTest extends KernelTestCase
{
    public function test_the_application_can_run(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('todo:list');
        $commandTester = new CommandTester($command);

        $commandTester->execute([], []);

        $this->assertSame("Test output\n", $commandTester->getDisplay());
    }
}
