<?php

class MapCursor implements Iterator
{
    private array $trail = [];
    private int $iteratorPosition = 0;

    public function __construct(public int $x, public int $y, public int $value = 0, ?MapCursor $other = null)
    {
        if ($other !== null) {
            $this->trail = $other->trail;
        }
        $this->addToTrail($x, $y);
    }

    public function addToTrail(int $x, int $y)
    {
        $this->trail[] = [$x, $y];
    }

    public function current(): mixed
    {
        return $this->trail[$this->iteratorPosition];
    }

    public function next(): void
    {
        $this->iteratorPosition++;
    }

    public function key(): mixed
    {
        return $this->iteratorPosition;
    }

    public function valid(): bool
    {
        return $this->iteratorPosition >= 0 && $this->iteratorPosition < count($this->trail);
    }

    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

    public function __toString(): string
    {
        return sprintf("[%d,%d]", $this->x, $this->y);
    }
}

class Map
{
    private const array DIRECTIONS = [
        [-1, 0],
        [0, -1],
        [1, 0],
        [0, 1]
    ];

    public readonly int $width;
    public readonly int $height;

    private string $mapData;

    private array $trailHeads = [];
    private array $cursors = [];

    public function __construct(array $mapRows, private bool $keepDistinctsPaths = false)
    {
        $this->width = strlen($mapRows[0]);
        $this->height = count($mapRows);

        $this->mapData = implode('', $mapRows);
    }

    public function getCell(int $x, int $y): int
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return 0;
        }
        return $this->mapData[$y * $this->width + $x];
    }

    public function findTrailHeads()
    {
        $trailHeaderNumber = 0;
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $cell = $this->getCell($x, $y);
                if ($cell === 0) {
                    $this->cursors = [new MapCursor($x, $y)];
                    if (($score = $this->computeTrailHeadScore()) > 0) {
                        $this->trailHeads[] = [$x, $y, $score];
                        printf("Trailhead #%d\n", $trailHeaderNumber++);
                        printf("  %d, %d\n", $x, $y);
                        foreach ($this->cursors as $cursor) {
                            printf("    Cursor: %d, %d | %d\n", $cursor->x, $cursor->y, $cursor->value);
                            //$this->dumpCursorMap($cursor, "      ");
                        }
                        printf("  Score: %d\n", count($this->cursors));
                    }
                }
            }
        }
    }

    private function computeTrailHeadScore(): int
    {
        while (($validCursorCount = $this->countValidCursors()) > 0) {
            for ($cursorIndex = count($this->cursors) - 1; $cursorIndex >= 0; $cursorIndex--) {
                $cursor = $this->cursors[$cursorIndex];
                if ($cursor->value == 9) {
                    continue;
                }

                $branches = [];
                foreach (self::DIRECTIONS as $direction) {
                    $nextX = $cursor->x + $direction[0];
                    $nextY = $cursor->y + $direction[1];

                    $nextCell = $this->getCell($nextX, $nextY);
                    if ($nextCell == $cursor->value + 1) {
                        $branches[] = [$nextX, $nextY];
                    }
                }

                if (!empty($branches)) {
                    for ($i = 1; $i < count($branches); $i++) {
                        $this->cursors[] = new MapCursor($branches[$i][0], $branches[$i][1], $cursor->value + 1, $cursor);
                    }

                    $cursor->x = $branches[0][0];
                    $cursor->y = $branches[0][1];
                    $cursor->value++;
                    $cursor->addToTrail($branches[0][0], $branches[0][1]);
                } else {
                    array_splice($this->cursors, $cursorIndex, 1);
                }
            }
        }

        $this->cursors = array_filter($this->cursors, fn($item) => $item->value == 9);
        if (!$this->keepDistinctsPaths) {
            $this->cursors = array_unique($this->cursors);
        }
        return count($this->cursors);
    }

    private function countValidCursors(): int
    {
        return array_reduce($this->cursors, fn(int $carry, MapCursor $item) => $carry + ($item->value < 9) ? 1 : 0, 0);
    }

    public function getTotalTrailHeadScore(): int
    {
        return array_reduce($this->trailHeads, fn($carry, $item) => $carry + $item[2], 0);
    }

    private function dumpCursorMap(MapCursor $cursor, string $linePrefix)
    {
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $found = false;
                foreach ($cursor as $cursorPosition) {
                    if ($cursorPosition[0] == $x && $cursorPosition[1] == $y) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    print('.');
                } else {
                    print($this->getCell($x, $y));
                }
            }
            print("\n");
        }
    }
}
