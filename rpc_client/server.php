<?php
define('SERVER_PATH', __DIR__ . '/');
$loader = include SERVER_PATH . '../autoload.php';

use EPS\Process\WorkerProcess;

WorkerProcess::instance('Server\\Process\\Main', 'rpc_client')->init()->run();
