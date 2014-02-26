<?php
define('SERVER_PATH', __DIR__ . '/');
define('LOG_FILE', __DIR__ . '/ttt.log');
$loader = include SERVER_PATH . '../autoload.php';

use EPS\Process\ChildProcess;

ChildProcess::instance('eps_main')
    ->setWorker('Server\\Process\\Main')
    ->setMain()
    //->setDaemon()
    ->run();