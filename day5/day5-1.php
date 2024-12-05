<?php

$lines = require_once('../common/input.php');

$orderingRules = [];
$pagesToPrint = [];

$separationFound = false;
foreach ($lines as $line) {
    if (empty($line)) {
        $separationFound = true;
        continue;
    }

    if ($separationFound) {
        $pagesToPrint[] = $line;
    } else {
        $orderingRules[] = $line;
    }
}

$orderingRules = array_map(fn($rule) => array_map(intval(...), explode('|', $rule)), $orderingRules);
$pagesToPrint = array_map(fn($list) => array_map(intval(...), explode(',', $list)), $pagesToPrint);

$middlePages = [];

foreach ($pagesToPrint as $list) {
    $listIsValid = true;
    foreach ($orderingRules as $rule) {
        $pageIndex1 = array_search($rule[0], $list);
        $pageIndex2 = array_search($rule[1], $list);

        if ($pageIndex1 === false || $pageIndex2 === false) {
            continue;
        }

        if ($pageIndex1 > $pageIndex2) {
            $listIsValid = false;
            break;
        }
    }
    if ($listIsValid) {
        $middleIndex = intval(count($list) / 2);
        $middlePages[] = $list[$middleIndex];
    }
}

var_dump(array_sum($middlePages));
