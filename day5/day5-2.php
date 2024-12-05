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

$incorrectLists = [];
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
    if (!$listIsValid) {
        $incorrectLists[] = $list;
    }
}

$middlePages = [];
foreach ($incorrectLists as $list) {
    $applicableRules = [];
    foreach ($orderingRules as $rule) {
        if (array_search($rule[0], $list) !== false && array_search($rule[1], $list) !== false) {
            $applicableRules[] = $rule;
        }
    }

    $ruleDictionary = [];
    foreach ($applicableRules as $rule) {
        if (array_key_exists($rule[0], $ruleDictionary)) {
            $ruleDictionary[$rule[0]][] = $rule[1];
        } else {
            $ruleDictionary[$rule[0]] = [$rule[1]];
        }
    }

    $orderedPages = [];
    foreach ($ruleDictionary as $k => $v) {
        if (count($v) == 1) {
            $orderedPages[] = $v[0];
            break;
        }
    }
    removePageFromDictionary($ruleDictionary, $orderedPages[0]);

    while (!empty($ruleDictionary)) {
        $keys = array_keys($ruleDictionary);
        foreach ($keys as $key) {
            if (count($ruleDictionary[$key]) == 0) {
                $page = $key;
                break;
            }
        }

        array_splice($orderedPages, 0, 0, [$page]);
        unset($ruleDictionary[$page]);
        removePageFromDictionary($ruleDictionary, $page);
    }

    $middleIndex = intval(count($orderedPages) / 2);
    $middlePages[] = $orderedPages[$middleIndex];
}

var_dump(array_sum($middlePages));

function removePageFromDictionary(array &$dictionary, int $page)
{
    $keys = array_keys($dictionary);
    foreach ($keys as $key) {
        $index = array_search($page, $dictionary[$key]);
        array_splice($dictionary[$key], $index, 1);
    }
}
