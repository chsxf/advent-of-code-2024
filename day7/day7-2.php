<?php

require_once('./Equation3Tester.php');

$lines = require_once('../common/input.php');

$i = 0;
$sum = 0;
foreach ($lines as $equation) {
    printf("Equadtion %d/%d\n", $i++, count($lines));

    $et = new Equation3Tester($equation);
    if ($et->hasSolution()) {
        $sum += $et->result;
    }
}
var_dump($sum);
