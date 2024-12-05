<?php

require_once('Grid2.php');

$fileName = 'input.txt';
if (!empty($argv[1]) && $argv[1] === 'sample') {
    $fileName = 'sample.txt';
}
$input = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$grid = new Grid($input);
var_dump($grid->countPatterns());
