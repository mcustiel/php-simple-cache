<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('mcustiel\\', __DIR__ . '/../src');
$loader->add('Unit\\SimpleCache\\', __DIR__ . '/unit/mcustiel/SimpleCache');
$loader->add('Functional\\SimpleCache\\', __DIR__ . '/functional/mcustiel/SimpleCache');
