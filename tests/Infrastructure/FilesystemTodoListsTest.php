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
    public function tearDown(): void
    {
        (new Filesystem())->remove('.data/test/list');
    }

    public function test_items_can_be_added_to_the_todo_list(): void
    {
        $todoList = new FilesystemTodoList('.data/test');
        $checkedItem = Item::new('new item 1');
        $checkedItem->check();

        $todoList->addAnItem($checkedItem);
        $todoList->addAnItem(Item::new('new item 2'));

        $this->assertCount(2, $todoList->list());
        $this->assertSame('new item 1', $todoList->list()[0]->label());
        $this->assertSame('new item 2', $todoList->list()[1]->label());
        $this->assertTrue($todoList->list()[0]->isChecked());
        $this->assertFalse($todoList->list()[1]->isChecked());
    }

    public function test_is_empty_returns_false_when_there_are_items(): void
    {
        $todoList = new FilesystemTodoList('.data/test');
        $todoList->addAnItem(Item::new('new item 1'));

        $this->assertFalse($todoList->isEmpty());
    }

    public function test_is_empty_returns_true_when_there_are_no_items(): void
    {
        $todoList = new FilesystemTodoList('.data/test');

        $this->assertTrue($todoList->isEmpty());
    }

    public function test_is_all_done_returns_false_when_there_are_unchecked_items(): void
    {
        $todoList = new FilesystemTodoList('.data/test');
        $todoList->addAnItem(Item::new('new item 1'));

        $this->assertFalse($todoList->isAllDone());
    }


    public function test_is_all_done_returns_true_when_there_are_no_unchecked_items(): void
    {
        $todoList = new FilesystemTodoList('.data/test');
        $item = Item::new('new item 1');
        $item->check();
        $todoList->addAnItem($item);

        $this->assertTrue($todoList->isAllDone());
    }


    public function test_is_all_done_returns_true_when_there_are_no_items_at_all(): void
    {
        $todoList = new FilesystemTodoList('.data/test');
        $this->assertTrue($todoList->isAllDone());
    }

    public function test_an_item_can_be_removed_from_the_todo_list(): void
    {
        $itemToRemove = Item::new('new item 2');

        $todoList = new FilesystemTodoList('.data/test');
        $todoList->addAnItem(Item::new('new item 1'));
        $todoList->addAnItem($itemToRemove);
        $todoList->addAnItem(Item::new('new item 3'));

        $todoList->removeAnItem($itemToRemove);

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
