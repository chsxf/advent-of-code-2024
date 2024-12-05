<?php

final class Grid
{
    public readonly int $width;
    public readonly int $height;

    private const array WORD_PATTERNS = [
        // TL, BL, TR, BR
        [['M', -1, -1], ['M', -1, 1], ['S', 1, -1], ['S', 1, 1]],
        [['M', -1, -1], ['S', -1, 1], ['M', 1, -1], ['S', 1, 1]],
        [['S', -1, -1], ['S', -1, 1], ['M', 1, -1], ['M', 1, 1]],
        [['S', -1, -1], ['M', -1, 1], ['S', 1, -1], ['M', 1, 1]]
    ];

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
        $patternCount = 0;
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $cell = $this->getCellValue($x, $y);
                if ($cell === 'A' && $this->hasPatternAroundPosition($x, $y)) {
                    printf('A');
                    $patternCount++;
                } else {
                    printf('.');
                }
            }
            printf("\n");
        }
        return $patternCount;
    }

    private function hasPatternAroundPosition(int $startX, int $startY): bool
    {
        foreach (self::WORD_PATTERNS as $pattern) {
            try {
                $isValidPattern = true;
                foreach ($pattern as $patternElement) {
                    $x = $startX + $patternElement[1];
                    $y = $startY + $patternElement[2];
                    $cell = $this->getCellValue($x, $y);
                    if ($cell != $patternElement[0]) {
                        $isValidPattern = false;
                        break;
                    }
                }
                if ($isValidPattern) {
                    return true;
                }
            } catch (Exception) {
            }
        }
        return false;
    }
}
