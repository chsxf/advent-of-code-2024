<?php

require_once('./EquationTester.php');

$lines = require_once('../common/input.php');

$sum = 0;
foreach ($lines as $equation) {
    $et = new EquationTester($equation);
    if ($et->hasSolution()) {
        $sum += $et->result;
    }
}
var_dump($sum);
