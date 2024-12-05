<?php

require_once('Grid2.php');

$input = require_once('../common/input.php');

$grid = new Grid($input);
var_dump($grid->countPatterns());
