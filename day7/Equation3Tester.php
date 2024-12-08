<?php

require_once('./Base3Number.php');

class Equation3Tester
{
    public readonly int $result;
    public readonly array $members;

    public readonly int $operatorCount;

    public function __construct(string $equation)
    {
        list($this->result, $memberList) = explode(':', $equation);

        $this->members = array_map(intval(...), explode(' ', trim($memberList)));
        $this->operatorCount = count($this->members) - 1;
    }

    public function hasSolution(): bool
    {
        $combinationCount = pow(3, $this->operatorCount);
        for ($i = 0; $i < $combinationCount; $i++) {
            $base3Number = new Base3Number($i);
            $resultForCombination = $this->members[0];
            for ($operatorIndex = 0; $operatorIndex < $this->operatorCount; $operatorIndex++) {
                $digit = $base3Number->getDigit($operatorIndex);
                switch ($digit) {
                    case 0:
                        $resultForCombination += $this->members[$operatorIndex + 1];
                        break;
                    case 1:
                        $resultForCombination *= $this->members[$operatorIndex + 1];
                        break;
                    case 2:
                        $resultForCombination .= $this->members[$operatorIndex + 1];
                        break;
                }
            }

            if ($resultForCombination == $this->result) {
                return true;
            }
        }
        return false;
    }
}
