<?php

require_once('Grid.php');

$lines = require_once('../common/input.php');

$grid = new Grid($lines);

for ($y = 0; $y < $grid->height; $y++) {
    printf("Row %d/%d\n", $y, $grid->height);
    for ($x = 0; $x < $grid->width; $x++) {
        if ($grid->startX == $x && $grid->startY == $y) {
            continue;
        }

        if ($grid->getLayoutValue($x, $y) == '#') {
            continue;
        }

        $grid->setLayoutValue($x, $y, '#');
        while (($result = $grid->computeNextMove(true)) === ComputeMoveResult::continue) {
        }
        if ($result === ComputeMoveResult::looping) {
            $grid->addPotentialObstruction($x, $y);
        }
        $grid->setLayoutValue($x, $y, '.');
        $grid->resetTraces();
    }
}

printf("Potential obstructions count: %d\n", $grid->getPotentialObstructionsCount());
