<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Todo\Domain\TodoList;

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
}
