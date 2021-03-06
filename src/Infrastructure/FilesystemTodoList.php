<?php

declare(strict_types=1);

namespace Todo\Infrastructure;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Todo\Domain\Item;
use Todo\Domain\SorryTheItemIsNotInTheList;
use Todo\Domain\TodoList;

final class FilesystemTodoList implements TodoList
{
    private string $fileName;
    private Filesystem $filesystem;
    private Finder $finder;
    private string $pathOfTheDataFile;
    private string $pathToTheDataFile;

    public function __construct(string $pathToTheDataFile, string $fileName = 'list')
    {
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
        $this->fileName = $fileName;
        $this->pathToTheDataFile = $pathToTheDataFile;

        $this->pathOfTheDataFile = sprintf('%s/%s', $this->pathToTheDataFile, $this->fileName);
        $this->filesystem->touch($this->pathOfTheDataFile);
    }

    public function addAnItem(Item $item): void
    {
        $this->filesystem->appendToFile(
            $this->pathOfTheDataFile,
            $this->fromItemToLine($item)
        );
    }

    public function isAllDone(): bool
    {
        foreach ($this->list() as $item) {
            if ($item->isChecked() === false) {
                return false;
            }
        }

        return true;
    }

    public function isEmpty(): bool
    {
        return count($this->list()) < 1;
    }

    /** @return array<Item> */
    public function list(): array
    {
        $lines = array_filter(
            explode("\n", $this->theDataFile()->getContents()),
            static fn (string $line) => $line !== ''
        );

        return array_map(
            [$this, 'fromLineToItem'],
            $lines
        );
    }

    public function moveDown(Item $itemToMoveDown): void
    {
        $items = $this->list();
        $oldPositionOfTheItemToMoveDown = null;

        foreach ($items as $position => $item) {
            if ($item->identifier() === $itemToMoveDown->identifier()) {
                $oldPositionOfTheItemToMoveDown = $position;

                // @codeCoverageIgnoreStart
                /**
                 * We ignore this, because the functionality _without_ the break is the same (there won't be two items
                 * with the same identifier, we assume) and that triggers a mutant to escape. We do want this break here
                 * for performance reasons (when we found the item, we don't need to loop through the rest of the list).
                 */

                break;
                // @codeCoverageIgnoreEnd
            }
        }

        if (!is_int($oldPositionOfTheItemToMoveDown)) {
            throw new SorryTheItemIsNotInTheList();
        }

        $lastPositionInTheList = count($items) - 1;
        $newPositionOfTheItemToMoveDown = $oldPositionOfTheItemToMoveDown === $lastPositionInTheList
            ? $lastPositionInTheList
            : $oldPositionOfTheItemToMoveDown + 1;
        $itemToMoveUp = $items[$newPositionOfTheItemToMoveDown];

        $items[$newPositionOfTheItemToMoveDown] = $itemToMoveDown;
        $items[$oldPositionOfTheItemToMoveDown] = $itemToMoveUp;

        $this->overwriteAllItems($items);
    }

    public function moveUp(Item $itemToMoveUp): void
    {
        $items = $this->list();
        $oldPositionOfTheItemToMoveUp = null;

        foreach ($items as $position => $item) {
            if ($item->identifier() === $itemToMoveUp->identifier()) {
                $oldPositionOfTheItemToMoveUp = $position;

                // @codeCoverageIgnoreStart
                /**
                 * We ignore this, because the functionality _without_ the break is the same (there won't be two items
                 * with the same identifier, we assume) and that triggers a mutant to escape. We do want this break here
                 * for performance reasons (when we found the item, we don't need to loop through the rest of the list).
                 */

                break;
                // @codeCoverageIgnoreEnd
            }
        }

        if (!is_int($oldPositionOfTheItemToMoveUp)) {
            throw new SorryTheItemIsNotInTheList();
        }

        $newPositionOfTheItemToMoveUp = $oldPositionOfTheItemToMoveUp === 0
            ? 0
            : $oldPositionOfTheItemToMoveUp - 1;
        $itemToMoveDown = $items[$newPositionOfTheItemToMoveUp];

        $items[$newPositionOfTheItemToMoveUp] = $itemToMoveUp;
        $items[$oldPositionOfTheItemToMoveUp] = $itemToMoveDown;

        $this->overwriteAllItems($items);
    }

    public function removeAnItem(Item $itemToRemove): void
    {
        $list = array_filter(
            $this->list(),
            static fn (Item $item) => $item->identifier() !== $itemToRemove->identifier()
        );

        $this->overwriteAllItems($list);
    }

    /** @param array<Item> $items */
    public function overwriteAllItems(array $items): void
    {
        $fileContents = '';

        foreach ($items as $item) {
            $fileContents .= $this->fromItemToLine($item);
        }

        $this->filesystem->dumpFile($this->pathOfTheDataFile, $fileContents);
    }

    /**
     * {@see self::fromLineToItem()}
     */
    private function fromItemToLine(Item $item): string
    {
        return sprintf(
            '%s;%s;%s',
            $item->identifier(),
            $item->isChecked() ? '1' : '0',
            $item->label()
        ) . "\n";
    }

    /**
     * A line contains, in order,
     * - a 32 character identifier (to prevent collisions when items have the same label)
     * - a ;
     * - a 0 or 1 indicating their check-status
     * - another ;
     * - and everything behind that is the label of the item
     *
     * This method is used as a callback in {@see self::list}, but phpcs thinks it's unused
     */
    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function fromLineToItem(string $lineFromATodoFile): Item
    {
        $identifier = substr($lineFromATodoFile, 0, 32);
        $isChecked = substr($lineFromATodoFile, 33, 1) === '1';
        $label = substr($lineFromATodoFile, 35);

        return Item::existing($identifier, $label, $isChecked);
    }

    private function theDataFile(): SplFileInfo
    {
        $file = current(iterator_to_array(
            $this->finder->in($this->pathToTheDataFile)->name($this->fileName)
        ));

        if (!$file instanceof SplFileInfo) {
            throw new SorryTheDataFileHasBeenRemoved();
        }

        return $file;
    }
}
