<?php

$lines = require_once('../common/input.php');

$reports = array_map(fn($line) => array_map(intval(...), explode(' ', $line)), $lines);

@unlink('output.txt');

$safeReportCount = 0;
foreach ($reports as $report) {
    $sign = null;
    $isValid = true;
    for ($i = 1; $i < count($report); $i++) {
        $diff = $report[$i] - $report[$i - 1];
        if (abs($diff) < 1 || abs($diff) > 3) {
            $isValid = false;
            break;
        }

        $newSign = $diff / abs($diff);
        if ($sign === null) {
            $sign = $newSign;
        } else if ($sign != $newSign) {
            $isValid = false;
            break;
        }
    }
    if ($isValid) {
        $safeReportCount++;
    }
}
var_dump($safeReportCount);
