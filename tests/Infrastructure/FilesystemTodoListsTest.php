<?php

declare(strict_types=1);

namespace Todo\Infrastructure;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Todo\Domain\Item;

/**
 * @covers \Todo\Infrastructure\FilesystemTodoList
 */
final class FilesystemTodoListsTest extends TestCase
{
    public function setUp(): void
    {
        (new Filesystem())->touch('.data/test/list');
    }

    public function tearDown(): void
    {
        (new Filesystem())->remove('.data/test/list');
    }

    public function test_items_can_be_added_to_the_todo_list(): void
    {
        $todoList = new FilesystemTodoList('.data/test');
        $todoList->addAnItem(Item::new('new item 1'));
        $todoList->addAnItem(Item::new('new item 2'));

        $this->assertCount(2, $todoList->list());
        $this->assertSame('new item 1', $todoList->list()[0]->label());
        $this->assertSame('new item 2', $todoList->list()[1]->label());
    }

    public function test_an_item_can_be_removed_from_the_todo_list(): void
    {
        $itemToRemove = Item::new('new item 2');

        $todoList = new FilesystemTodoList('.data/test');
        $todoList->addAnItem(Item::new('new item 1'));
        $todoList->addAnItem($itemToRemove);
        $todoList->addAnItem(Item::new('new item 3'));

        $todoList->removeItem($itemToRemove);

        $this->assertCount(2, $todoList->list());
        $this->assertSame('new item 1', $todoList->list()[0]->label());
        $this->assertSame('new item 3', $todoList->list()[1]->label());
    }

    public function test_a_disappearing_data_file_triggers_an_exception(): void
    {
        $this->expectException(SorryTheDataFileHasBeenRemoved::class);

        $todoList = new FilesystemTodoList('.data/test');

        // Remove the data file, mimicking someone from the outside removing it from disk
        (new Filesystem())->remove('.data/test/list');

        $todoList->list();
    }
}
