<?php

define('EPSERVER_PATH', __DIR__ . '/');
$loader = include EPSERVER_PATH . '../autoload.php';
$manager = new EP\Process\Manager('EPTcpGatewaySvr');
$manager->addChildren('EPServer\Process\\Reactor', 1);
$manager->run();