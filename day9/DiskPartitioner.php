<?php

class DiskFile
{
    private array $sectors;

    public function __construct(public readonly int $id, int $startPosition, int $length)
    {
        $this->sectors = [];
        for ($i = 0; $i < $length; $i++) {
            $this->sectors[] = $startPosition + $i;
        }
    }

    public function getMinSector(): int
    {
        return array_reduce($this->sectors, min(...), PHP_INT_MAX);
    }

    public function getMaxSector(): int
    {
        return array_reduce($this->sectors, max(...), 0);
    }

    public function reassignLastSector(int $to)
    {
        $lastIndex = count($this->sectors) - 1;
        $this->sectors[$lastIndex] = $to;
        sort($this->sectors);
    }

    public function contains(int $index): bool
    {
        return array_search($index, $this->sectors) !== false;
    }

    public function hasSectorAfter(int $index): bool
    {
        foreach ($this->sectors as $sector) {
            if ($sector > $index) {
                return true;
            }
        }
        return false;
    }

    public function moveTo(int $firstIndex)
    {
        $currentFirst = array_reduce($this->sectors, min(...), PHP_INT_MAX);
        for ($i = 0; $i < count($this->sectors); $i++) {
            $this->sectors[$i] += $firstIndex - $currentFirst;
        }
    }

    public function getLength()
    {
        return count($this->sectors);
    }
}

class DiskPartioner
{
    private array $files;
    private array $emptySpaces;

    public function __construct(string $diskPattern)
    {
        $this->files = [];
        $this->emptySpaces = [];

        $currentId = 0;
        $startPosition = 0;
        $totalWrittenSectors = 0;
        for ($i = 0; $i < strlen($diskPattern); $i++) {
            $value = intval($diskPattern[$i]);
            if ($i % 2 == 0) {
                $totalWrittenSectors += $value;
                $newFile = new DiskFile($currentId++, $startPosition, $value);
                $this->files[] = $newFile;
            } else {
                $this->emptySpaces[] = [$startPosition, $value];
            }
            $startPosition += $value;
        }

        print("  Total sectors: {$startPosition} (Written: {$totalWrittenSectors})\n");
    }

    public function defragment()
    {
        $currentIndex = 0;
        while (true) {
            if ($currentIndex > 0 && $currentIndex % 500 == 0) {
                print("  Sector {$currentIndex}\n");
            }
            if ($this->isEmpty($currentIndex)) {
                $lastFileAfter = $this->getLastFileAfter($currentIndex);
                if ($lastFileAfter === null) {
                    break;
                }

                $lastFileAfter->reassignLastSector($currentIndex);
            }

            $currentIndex++;
        }
    }

    public function defragmentWholeFiles()
    {
        for ($i = count($this->files) - 1; $i >= 0; $i--) {
            if ($i % 500 == 0 && $i > 0) {
                printf("  %d files remaining\n", $i);
            }

            $file = $this->files[$i];
            $firstEmptySpaceIndex = $this->findFirstEmptySpacePosition($file->getLength());
            if ($firstEmptySpaceIndex !== null && $file->getMinSector() > $firstEmptySpaceIndex) {
                $file->moveTo($firstEmptySpaceIndex);
            }
        }
    }

    public function dump()
    {
        $maxIndex = $this->getFileMaxIndex();
        for ($i = 0; $i <= $maxIndex; $i++) {
            $id = $this->getFileIdAt($i);
            print($id ?? '.');
        }
        print("\n");
    }

    private function getFileMaxIndex(): int
    {
        return array_reduce($this->files, fn($carry, $file) => max($carry, $file->getMaxSector()), 0);
    }

    private function findFirstEmptySpacePosition(int $length): ?int
    {
        $maxIndex = $this->getFileMaxIndex();

        for ($i = 0; $i < count($this->emptySpaces); $i++) {
            $emptySpace = $this->emptySpaces[$i];
            if ($emptySpace[0] > $maxIndex) {
                break;
            }

            if ($emptySpace[1] >= $length) {
                $firstSpaceIndex = $emptySpace[0];
                if ($emptySpace[1] == $length) {
                    array_splice($this->emptySpaces, $i, 1);
                } else {
                    $emptySpace[0] += $length;
                    $emptySpace[1] -= $length;
                    $this->emptySpaces[$i] = $emptySpace;
                }
                return $firstSpaceIndex;
            }
        }

        return null;
    }

    private function isEmpty(int $index): bool
    {
        foreach ($this->files as $file) {
            if ($file->contains($index)) {
                return false;
            }
        }
        return true;
    }

    private function getLastFileAfter(int $index): ?DiskFile
    {
        for ($i = count($this->files) - 1; $i >= 0; $i--) {
            $file = $this->files[$i];
            if ($file->hasSectorAfter($index)) {
                return $file;
            }
        }
        return null;
    }

    private function getFileIdAt(int $index): ?int
    {
        foreach ($this->files as $file) {
            if ($file->contains($index)) {
                return $file->id;
            }
        }
        return null;
    }

    public function computeChecksum(): int
    {
        $checkSum = 0;
        $maxIndex = $this->getFileMaxIndex();
        for ($i = 0; $i <= $maxIndex; $i++) {
            $id = $this->getFileIdAt($i);
            if ($id !== null) {
                $checkSum += $id * $i;
            }
        }
        return $checkSum;
    }
}
