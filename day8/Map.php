<?php

class Map
{
    private readonly int $width;
    private readonly int $height;

    private readonly array $antennasByFrequency;

    private array $antinodesByFrequency;

    public function __construct(array $gridRows, private readonly int $distanceMultiplier, private readonly int $loops)
    {
        $this->height = count($gridRows);
        $this->width = strlen($gridRows[0]);

        $detectedAntennasByFrequency = [];
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $frequency = strval($gridRows[$y][$x]);
                if ($frequency !== '.') {
                    if (array_key_exists($frequency, $detectedAntennasByFrequency)) {
                        $detectedAntennasByFrequency[$frequency][] = [$x, $y];
                    } else {
                        $detectedAntennasByFrequency[$frequency] = [[$x, $y]];
                    }
                }
            }
        }
        $this->antennasByFrequency = $detectedAntennasByFrequency;
    }

    public function detectAntinodes()
    {
        $frequencies = array_keys($this->antennasByFrequency);
        $antinodeLists = array_pad([], count($frequencies), []);
        $this->antinodesByFrequency = array_combine($frequencies, $antinodeLists);

        foreach ($this->antennasByFrequency as $frequency => $antennas) {
            for ($i = 0; $i < count($antennas) - 1; $i++) {
                $antennaA = $antennas[$i];
                for ($j = $i + 1; $j < count($antennas); $j++) {
                    $antennaB = $antennas[$j];

                    $vector = [$antennaB[0] - $antennaA[0], $antennaB[1] - $antennaA[1]];
                    for ($l = 1; $l <= $this->loops; $l++) {
                        $displacementVector = array_map(fn($item) => $item * $l * $this->distanceMultiplier, $vector);
                        $antinode1 = $this->combineVectors($antennaA, $displacementVector);
                        if ($this->isInMap($antinode1)) {
                            $this->antinodesByFrequency[$frequency][] = $antinode1;
                        } else {
                            break;
                        }
                    }

                    $inverseVector = array_map(fn($item) => -$item, $vector);
                    for ($l = 1; $l <= $this->loops; $l++) {
                        $displacementVector = array_map(fn($item) => $item * $l * $this->distanceMultiplier, $inverseVector);
                        $antinode2 = $this->combineVectors($antennaB, $displacementVector);
                        if ($this->isInMap($antinode2)) {
                            $this->antinodesByFrequency[$frequency][] = $antinode2;
                        } else {
                            break;
                        }
                    }
                }
            }
        }
    }

    public function countUniqueAntinodes(): int
    {
        $allAntinodes = [];
        foreach ($this->antinodesByFrequency as $antinodes) {
            $allAntinodes = array_merge($allAntinodes, $antinodes);
        }

        $allUniqueAntinodes = array_unique($allAntinodes, SORT_REGULAR);
        return count($allUniqueAntinodes);
    }

    private function isInMap(array $position): bool
    {
        return !($position[0] < 0 || $position[0] >= $this->width || $position[1] < 0 || $position[1] >= $this->height);
    }

    private function combineVectors(array $v1, array $v2): array
    {
        return [
            $v1[0] + $v2[0],
            $v1[1] + $v2[1]
        ];
    }
}
