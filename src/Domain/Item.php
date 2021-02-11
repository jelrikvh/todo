<?php

declare(strict_types=1);

namespace Todo\Domain;

final class Item
{
    private string $identifier;
    private bool $isChecked;
    private string $label;

    private function __construct(string $identifier, string $label, bool $isChecked = false)
    {
        $this->identifier = $identifier;
        $this->isChecked = $isChecked;
        $this->label = $label;
    }

    public static function new(string $label): self
    {
        return new self(md5((string) rand(1, 9999999999)), $label);
    }

    public static function existing(string $identifier, string $label, bool $isChecked): self
    {
        return new self($identifier, $label, $isChecked);
    }

    public function check(): void
    {
        $this->isChecked = true;
    }

    public function identifier(): string
    {
        return $this->identifier;
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
