<?php

class EquationTester
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
        $combinationCount = pow(2, $this->operatorCount);
        for ($i = 0; $i < $combinationCount; $i++) {
            $resultForCombination = $this->members[0];
            for ($operatorIndex = 0; $operatorIndex < $this->operatorCount; $operatorIndex++) {
                $bit = $i & (1 << $operatorIndex);
                if (empty($bit)) {
                    $resultForCombination += $this->members[$operatorIndex + 1];
                } else {
                    $resultForCombination *= $this->members[$operatorIndex + 1];
                }
            }
            if ($resultForCombination == $this->result) {
                return true;
            }
        }
        return false;
    }
}
