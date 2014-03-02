<?php
define('SERVER_PATH', __DIR__ . '/');
define('LOG_FILE', __DIR__ . '/ttt.log');
$loader = include SERVER_PATH . '../autoload.php';

use EPS\Process\MainProcess;

MainProcess::instance('eps_main')
    ->setWorker('Server\\Worker\\Main')
    ->setMain()
    //->setDaemon()
    ->run();


//测试Server
// use EPS\Net\Server;
// use EPS\Standard\Message;

// cli_set_process_title('eps');

// $server = Server::instance();
// $server->setServerMessage(new Message(5501));
// $server->setSendMessage(new Message(5502, false));
// $server->setBoardcastMessage(new Message(5503, false));
// $server->listen(5501)->start();