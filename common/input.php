<?php

$fileName = 'input.txt';
if (!empty($argv[1]) && $argv[1] === 'sample') {
    $fileName = 'sample.txt';
}
return file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
