<?php

declare(strict_types=1);

namespace Todo\Domain;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Todo\Domain\Item
 */
class ItemTest extends TestCase
{
    public function test_a_new_item_can_be_created(): void
    {
        $item = Item::new('test todo item');

        $this->assertSame(32, strlen($item->identifier()));
        $this->assertSame('test todo item', $item->label());
        $this->assertSame(false, $item->isChecked());
    }

    public function test_an_already_existing_item_can_be_instantiated(): void
    {
        $identifier = 'abcdefghabcdefghabcdefghabcdefgh';
        $label = 'existing todo item';
        $isChecked = true;

        $item = Item::existing($identifier, $label, $isChecked);

        $this->assertSame($identifier, $item->identifier());
        $this->assertSame($label, $item->label());
        $this->assertSame($isChecked, $item->isChecked());
    }

    public function test_an_item_can_be_checked_and_unchecked_again(): void
    {
        $item = Item::new('check toggle todo item');

        $item->check();
        $this->assertSame(true, $item->isChecked());

        $item->uncheck();
        $this->assertSame(false, $item->isChecked());
    }

    public function test_that_checking_when_it_is_already_checked_does_not_generate_an_error(): void
    {
        $item = Item::new('check toggle todo item');

        $item->check();
        $item->check();

        $this->assertSame(true, $item->isChecked());
    }

    public function test_that_unchecking_when_it_is_already_unchecked_does_not_generate_an_error(): void
    {
        $item = Item::new('check toggle todo item');

        $item->uncheck();
        $item->uncheck(); // This is a bit overkill, as the Item is unchecked by default, but better safe than sorry.

        $this->assertSame(false, $item->isChecked());
    }
}
