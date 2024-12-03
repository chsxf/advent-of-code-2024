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

$frequency = array_count_values($lists[1]);

$similarityScore = 0;
foreach ($lists[0] as $entry) {
    $count = array_key_exists($entry, $frequency) ? $frequency[$entry] : 0;
    $similarityScore += $entry * $count;
}
var_dump($similarityScore);