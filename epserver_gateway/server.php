<?php
define('SERVER_PATH', __DIR__ . '/');
$loader = include SERVER_PATH . '../autoload.php';

use EPS\Process\MainProcess;

MainProcess::instance('Server\\Process\\Main', 'eps_main')->fork();
