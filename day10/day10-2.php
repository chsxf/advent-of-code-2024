<?php

require_once('./Map.php');

$lines = require_once('../common/input.php');

$map = new Map($lines, true);
$map->findTrailHeads();
var_dump($map->getTotalTrailHeadScore());
