<?php

$lines = require_once('../common/input.php');

$totalSum = 0;
foreach ($lines as $line) {
    preg_match_all('/mul\((\d{1,3}),(\d{1,3})\)/', $line, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $product = $match[1] * $match[2];
        $totalSum += $product;
    }
}
var_dump($totalSum);
