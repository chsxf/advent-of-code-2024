<?php

final class Grid
{
    public readonly int $width;
    public readonly int $height;

    private const array WORD_DIRECTIONS = [
        [-1, 0], // Backwards
        [-1, -1], // Backwards and upwards
        [-1, 1], // Backwards and downwards
        [0, -1], // Upwards
        [0, 1], // Downwards
        [1, 0], // Forward
        [1, -1], // Forward and upwards
        [1, 1] // Forward and downwards
    ];

    private const string WORD_TO_FIND = 'XMAS';

    public function __construct(private array $gridData)
    {
        $this->height = count($gridData);
        $this->width = strlen($gridData[0]);
    }

    private function getCellValue(int $x, int $y): string
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            throw new Exception("Coordinate {$x},{$y} is outside the grid");
        }
        return $this->gridData[$y][$x];
    }

    public function countPatterns(): int
    {
        $wordCount = 0;
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $cell = $this->getCellValue($x, $y);
                if ($cell === 'X') {
                    $wordCount += $this->countWordsAroundPosition($x, $y);
                }
            }
        }
        return $wordCount;
    }

    private function countWordsAroundPosition(int $startX, int $startY): int
    {
        $count = 0;
        foreach (self::WORD_DIRECTIONS as $direction) {
            $wordIsOk = true;
            for ($i = 1; $i < strlen(self::WORD_TO_FIND); $i++) {
                try {
                    $letter = self::WORD_TO_FIND[$i];
                    $cell = $this->getCellValue($startX + $i * $direction[0], $startY + $i * $direction[1]);
                    if ($cell != $letter) {
                        $wordIsOk = false;
                        break;
                    }
                } catch (Exception) {
                    $wordIsOk = false;
                    break;
                }
            }
            if ($wordIsOk) {
                $count++;
            }
        }
        return $count;
    }
}
