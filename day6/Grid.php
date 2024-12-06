<?php

enum Direction: int
{
    case up = 0x1;
    case down = 0x2;
    case left = 0x4;
    case right = 0x8;

    public function turnRight(): Direction
    {
        return match ($this) {
            self::up => self::right,
            self::right => self::down,
            self::down => self::left,
            self::left => self::up
        };
    }

    public function offset(): array
    {
        return match ($this) {
            self::up => [0, -1],
            self::right => [1, 0],
            self::down => [0, 1],
            self::left => [-1, 0]
        };
    }

    public static function getChar(int $directionFlags): string
    {
        return match ($directionFlags) {
            0 => '.',
            Direction::up->value => '^',
            Direction::down->value => 'v',
            Direction::right->value => '>',
            Direction::left->value => '<',
            default => '+'
        };
    }
}

enum ComputeMoveResult
{
    case continue;
    case looping;
    case outOfMap;
}

final class Grid
{
    public readonly int $width;
    public readonly int $height;

    private string $layout;
    private array $traces;

    public readonly int $startX;
    public readonly int $startY;

    private int $guardX;
    private int $guardY;
    private Direction $guardDirection;

    private array $potentialObstructions = [];

    public function __construct(array $gridData)
    {
        $this->height = count($gridData);
        $this->width = strlen($gridData[0]);

        $this->layout = implode('', $gridData);
        $guardIndex = strpos($this->layout, '^');
        $this->layout = str_replace('^', '.', $this->layout);
        $this->startX = $guardIndex % $this->width;
        $this->startY = intval($guardIndex / $this->width);

        $this->resetTraces();
    }

    public function getLayoutValue(int $x, int $y): string
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            throw new Exception("Coordinate {$x},{$y} is outside the grid");
        }
        return $this->layout[($y * $this->width) + $x];
    }

    public function setLayoutValue(int $x, int $y, string $value)
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            throw new Exception("Coordinate {$x},{$y} is outside the grid");
        }
        $this->layout[($y * $this->width) + $x] = $value;
    }

    public function resetTraces()
    {
        $this->traces = array_pad([], strlen($this->layout), 0);
        $this->setTrace($this->startX, $this->startY, Direction::up);

        $this->guardX = $this->startX;
        $this->guardY = $this->startY;
        $this->guardDirection = Direction::up;
    }

    private function getTrace(int $x, int $y): int
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            throw new Exception("Coordinate {$x},{$y} is outside the grid");
        }
        return $this->traces[($y * $this->width) + $x];
    }

    private function setTrace(int $x, int $y, Direction $direction)
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            throw new Exception("Coordinate {$x},{$y} is outside the grid");
        }
        $index = ($y * $this->width) + $x;
        $this->traces[$index] |= $direction->value;
    }

    public function dump()
    {
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $layoutCell = $this->getLayoutValue($x, $y);
                $traceCell = $this->getTrace($x, $y);
                if ($layoutCell != '.') {
                    printf("#");
                } else {
                    printf('%s', Direction::getChar($traceCell));
                }
            }
            printf("\n");
        }

        printf("\n");
        printf("Obstructions:\n");
        foreach ($this->potentialObstructions as $po) {
            vprintf(" - %d : %d\n", $po);
        }
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $layoutCell = $this->getLayoutValue($x, $y);
                if ($layoutCell != '.') {
                    printf("#");
                } else if ($this->isObstruction($x, $y)) {
                    printf('%s', 'O');
                } else {
                    printf('.');
                }
            }
            printf("\n");
        }
    }

    private function isObstruction(int $x, int $y): bool
    {
        foreach ($this->potentialObstructions as $po) {
            if ($po[0] == $x && $po[1] == $y) {
                return true;
            }
        }
        return false;
    }

    public function computeNextMove(bool $checkForExistingTraces = false): ComputeMoveResult
    {
        $offsetForCurrentDirection = $this->guardDirection->offset();
        $nextX = $this->guardX + $offsetForCurrentDirection[0];
        $nextY = $this->guardY + $offsetForCurrentDirection[1];

        try {
            $layoutValue = $this->getLayoutValue($nextX, $nextY);

            if ($checkForExistingTraces) {
                $traceValue = $this->getTrace($nextX, $nextY);
                if (($traceValue & $this->guardDirection->value) != 0) {
                    return ComputeMoveResult::looping;
                }
            }
        } catch (Exception) {
            return ComputeMoveResult::outOfMap;
        }

        if ($layoutValue == '#') {
            $this->guardDirection = $this->guardDirection->turnRight();
            $this->setTrace($this->guardX, $this->guardY, $this->guardDirection);
            return $this->computeNextMove($checkForExistingTraces);
        }

        $this->guardX = $nextX;
        $this->guardY = $nextY;
        $this->setTrace($this->guardX, $this->guardY, $this->guardDirection);
        return ComputeMoveResult::continue;
    }

    public function countUniqueTraces(): int
    {
        return ($this->width * $this->height) - array_count_values($this->traces)[0];
    }

    public function addPotentialObstruction(int $x, int $y)
    {
        $this->potentialObstructions[] = [$x, $y];
    }

    public function getPotentialObstructionsCount(): int
    {
        return count($this->potentialObstructions);
    }
}
