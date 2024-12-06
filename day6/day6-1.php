<?php

require_once('Grid.php');

$lines = require_once('../common/input.php');

$grid = new Grid($lines);

$step = 0;
while ($grid->computeNextMove() === ComputeMoveResult::continue) {
    $step++;
}
printf("Total compute steps: %d\n", $step);
$grid->dump();
printf("Unique traces: %d\n", $grid->countUniqueTraces());
