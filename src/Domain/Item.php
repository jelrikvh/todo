<?php

declare(strict_types=1);

namespace Todo\Domain;

final class Item
{
    private bool $isChecked = false;
    private string $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function check(): void
    {
        $this->isChecked = true;
    }

    public function isChecked(): bool
    {
        return $this->isChecked;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function uncheck(): void
    {
        $this->isChecked = false;
    }
}
