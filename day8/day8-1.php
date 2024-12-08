<?php

require_once('Map.php');

$lines = require_once('../common/input.php');

$map = new Map($lines, 2, 1);
$map->detectAntinodes();

var_dump($map->countUniqueAntinodes());
