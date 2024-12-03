<?php

$inputLines = file('input.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
$inputLines = array_map(function($line) {
    $line = preg_replace('/\s+/', ' ', $line);
    return array_map(intval(...), explode(' ', $line));
}, $inputLines);

$lists = [[], []];
foreach ($inputLines as $line) {
    $lists[0][] = $line[0];
    $lists[1][] = $line[1];
}

sort($lists[0]);
sort($lists[1]);

$totalDistance = 0;
for ($i = 0; $i < count($lists[0]); $i++) {
    $totalDistance += abs($lists[1][$i] - $lists[0][$i]);
}
var_dump($totalDistance);