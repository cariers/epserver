<?php
define('SERVER_PATH', __DIR__ . '/');
$loader = include SERVER_PATH . '../autoload.php';

use EPS\Standard\Message;

$msg = new Message(700520, 0666, false);

$i = 0;
$time = microtime(true);
while (true) {
    $msg->recv();
}
echo microtime(true) - $time;
