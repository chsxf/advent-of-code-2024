<?php

require_once('Map.php');

$lines = require_once('../common/input.php');

$map = new Map($lines, 1, PHP_INT_MAX);
$map->detectAntinodes();

var_dump($map->countUniqueAntinodes());
