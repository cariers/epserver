<?php
define('SERVER_PATH', __DIR__ . '/');
$loader = include SERVER_PATH . '../autoload.php';

use EPS\Standard\Message;

$msg = new Message(700520);
echo 'start>>';
$i = 0;
$time = microtime(true);
while (++$i < 100000) {
    if ($i % 10000 == 0) {
        echo $i .'>>';
    }
    $msg->send(['abc']);
}
echo microtime(true) - $time;
