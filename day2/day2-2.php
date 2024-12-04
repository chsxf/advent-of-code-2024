<?php

$lines = file('input.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
$reports = array_map(fn($line) => array_map(intval(...), explode(' ', $line)), $lines);

$safeReportCount = 0;
foreach ($reports as $report) {
    if (checkReportWithDampener($report)) {
        $safeReportCount++;
    }
}
var_dump($safeReportCount);

function checkReportWithDampener(array $report): bool
{
    if (checkReport($report)) {
        return true;
    }

    for ($i = 0; $i < count($report); $i++) {
        $dampenedReport = $report;
        array_splice($dampenedReport, $i, 1);
        if (checkReport($dampenedReport)) {
            return true;
        }
    }

    return false;
}

function checkReport(array $report): bool
{
    $isValid = true;
    $sign = null;
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
    return $isValid;
}
