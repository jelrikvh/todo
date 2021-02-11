<?php

declare(strict_types=1);

namespace Todo\Infrastructure;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Todo\Domain\Item;
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
    }

    public function addAnItem(Item $item): void
    {
        $this->filesystem->appendToFile(
            $this->pathOfTheDataFile,
            $this->fromItemToLine($item)
        );
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
