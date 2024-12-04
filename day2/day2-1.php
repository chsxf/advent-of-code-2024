<?php

$lines = file('input.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
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

    file_put_contents('output.txt', sprintf("%s => %d\n", implode(' ', $report), $isValid), FILE_APPEND);
}
var_dump($safeReportCount);
