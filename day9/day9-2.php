<?php

require_once('./DiskPartitioner.php');

$line = require_once('../common/input.php');

printf("Building...\n");
$dp = new DiskPartioner($line[0]);
printf("Defragmenting...\n");
$dp->defragmentWholeFiles();
printf("Computing checkum...\n");
var_dump($dp->computeChecksum());
