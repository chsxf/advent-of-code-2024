<?php

class Base3Number
{
    private string $base3Representation;

    public function __construct(int $base10Number)
    {
        $this->base3Representation = base_convert($base10Number, 10, 3);
    }

    public function increment()
    {
        $base10 = base_convert($this->base3Representation, 3, 10);
        $base10 += 1;
        $this->base3Representation = base_convert($base10, 10, 3);
    }

    public function getDigit(int $index): int
    {
        $representationLength = strlen($this->base3Representation);
        if ($index >= $representationLength) {
            return 0;
        }
        return $this->base3Representation[strlen($this->base3Representation) - 1 - $index];
    }
}
