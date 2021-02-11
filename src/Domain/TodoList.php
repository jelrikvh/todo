<?php

declare(strict_types=1);

namespace Todo\Domain;

final class TodoList
{
    /** @var array<Item> */
    private array $items = [];

    public function __construct(Item ...$items)
    {
        $this->items = $items;
    }

    /** @return array<Item> */
    public function items(): array
    {
        return $this->items;
    }

    public function add(Item $item): void
    {
        $this->items[] = $item;
    }

    public function remove(Item $itemToRemove): void
    {
        $this->items = array_values(array_filter($this->items, function ($itemToInspect) use ($itemToRemove) {
            return ($itemToRemove !== $itemToInspect);
        }));
    }
}
