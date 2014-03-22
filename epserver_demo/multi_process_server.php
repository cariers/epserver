<?php
define('SERVER_PATH', __DIR__ . '/');

$loader = include SERVER_PATH . '../autoload.php';

use EPS\Bootstrap\MultiProcessServer;

$server = [
    'driver' => 'EPS\\Net\\ServerDriver\\Swoole',
    'host' => '0.0.0.0',
    'port' => 5501,
    'type' => 'TCP',
    'setting' => [
        'worker_num' => 3,
        'task_worker_num' => 3
    ]
];

$dispatcher = [
    'dispatcher' => 'EPS\\ServerDispatcher\\MessageDispatcher',
    'acceptMessage' => [
        'driver'  => 'EPS\\Driver\\Message\\SystemIPC',
        'message' => 'Accept5501',
        'option'  => [
            'perms' => 0666,
            'reset' => true
        ]
    ],
    'sendMessage' => [
        'driver'  => 'EPS\\Driver\\Message\\SystemIPC',
        'message' => 'Send5501',
        'option'  => [
            'perms' => 0666,
            'reset' => true
        ]
    ],
    'receiveMessage' => [
        'driver'  => 'EPS\\Driver\\Message\\SystemIPC',
        'message' => 'Receive5501',
        'option'  => [
            'perms' => 0666,
            'reset' => true
        ]
    ],
];

$logic = [
    'dispatchLogic' => 'Server\\Logic\\DispatchLogic',
    'workerNum' => 3
];

MultiProcessServer::instance('ep_multi_process_server')
    ->setServerOption($server)
    ->setServerDispatcherOption($dispatcher)
    ->setLogicOption($logic)
    ->run();