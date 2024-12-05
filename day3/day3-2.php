<?php

$lines = require_once('../common/input.php');

$totalSum = 0;
$multiply = true;
foreach ($lines as $line) {
    preg_match_all('/(?:do\(\)|don\'t\(\)|mul\((\d{1,3}),(\d{1,3})\))/', $line, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        if ($match[0] == 'do()') {
            $multiply = true;
        } else if ($match[0] == 'don\'t()') {
            $multiply = false;
        } else if ($multiply) {
            $product = $match[1] * $match[2];
            $totalSum += $product;
        }
    }
}
var_dump($totalSum);
