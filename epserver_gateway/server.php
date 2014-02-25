<?php
define('SERVER_PATH', __DIR__ . '/');
$loader = include SERVER_PATH . '../autoload.php';

use EPS\Process\ChildProcess;

ChildProcess::instance('eps_main')
    ->setWorker('Server\\Process\\MainTest')
    ->setMain()
    ->setDaemon()
    ->run();


//use EPS\Process\MainProcess;

//MainProcess::instance('Server\\Process\\Main', 'eps_main')->fork();
