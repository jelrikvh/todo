<?php

declare(strict_types=1);

namespace Todo\Domain;

interface TodoList
{
    public function addAnItem(Item $item): void;

    /** @return array<Item> */
    public function list(): array;

    public function removeAnItem(Item $itemToRemove): void;

    /** @param array<Item> $items */
    public function overwriteAllItems(array $items): void;
}
