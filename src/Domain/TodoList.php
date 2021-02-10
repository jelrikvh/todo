<?php

declare(strict_types=1);

namespace Todo\Domain;

final class TodoList
{
    /** @var array<Item> */
    private array $items = [];

    /** @return array<Item> */
    public function items(): array
    {
        return $this->items;
    }
}
