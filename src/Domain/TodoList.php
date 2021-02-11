<?php

declare(strict_types=1);

namespace Todo\Domain;

interface TodoList
{
    public function addAnItem(Item $item): void;

    public function isAllDone(): bool;

    public function isEmpty(): bool;

    /** @return array<Item> */
    public function list(): array;

    public function removeAnItem(Item $itemToRemove): void;

    /** @param array<Item> $items */
    public function overwriteAllItems(array $items): void;
}
