<?php

declare(strict_types=1);

namespace Todo\Domain;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Todo\Domain\TodoList
 */
final class TodoListTest extends TestCase
{
    public function test_a_todo_list_without_items_returns_an_empty_array(): void
    {
        $todoList = new TodoList();
        $this->assertSame([], $todoList->items());
    }

    public function test_a_todo_list_can_be_created_with_items_in_it(): void
    {
        $item1 = new Item();
        $item2 = new Item();

        $todoList = new TodoList($item1, $item2);

        $this->assertSame([$item1, $item2], $todoList->items());
    }

    public function test_we_can_add_items_to_a_todo_list(): void
    {
        $todoList = new TodoList();
        $item1 = new Item();
        $item2 = new Item();

        $todoList->add($item1);
        $todoList->add($item2);

        $this->assertSame([$item1, $item2], $todoList->items());
    }

    public function test_we_can_remove_an_item_from_a_todo_list(): void
    {
        $todoList = new TodoList();
        $item1 = new Item();
        $item2 = new Item();
        $todoList->add($item1);
        $todoList->add($item2);

        $todoList->remove($item1);

        $this->assertSame([$item2], $todoList->items());
    }
}
