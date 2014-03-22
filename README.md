服务开发框架 epserver V1.1
=====
你所要关心只有业务逻辑
-----
* 事件驱动
* 多进程逻辑
* 配置驱动

依赖扩展
-----
* [PHP 5.5](http://php.net)
* [swoole](https://github.com/matyhtf/swoole)
* pcntl --enable-pcntl
* [libev](https://github.com/ewenlaz/php-libev)
* System IPC --enable-sysvmsg --enable-sysvsem --enable-sysvshm


Example
-----

__多进程服务 [demo](https://github.com/ewenlaz/epserver/blob/master/epserver_demo/multi_process_server.php)__
```php
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
//MultiProcessServer::instance 第二个参数为 开启守护
MultiProcessServer::instance('ep_multi_process_server')
    ->setServerOption($server)
    ->setServerDispatcherOption($dispatcher)
    ->setLogicOption($logic)
    ->run();
```

__分发逻辑 [demo](https://github.com/ewenlaz/epserver/blob/master/epserver_demo/src/Logic/DispatchLogic.php)__
```php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace Server\Logic;

use EPS\ServerDispatcher\AbstractDispatchLogic;

class DispatchLogic extends AbstractDispatchLogic
{
    public function onAccept($sid, $connection) {
        echo sprintf("accept[%s]>>%s[%d]\n", $sid, $connection->ip, $connection->port);
        $this->send($sid, 'hello ' . $sid . "\n");
        $this->boardcast($sid . ' join room ~' . "\n");
    }
    public function onReceive($sid, $data) {
        $data = str_replace(["\n", "\r"], '', $data);
        if ($data == 'close') {
            $this->boardcast($sid . ' logout' . "\n");
            $this->close($sid);
        } else {
            $this->send($sid, 'you say ' . $data . "\n");
        }
        echo sprintf("receive[%s]>>%s\n", $sid, $data);
    }
    public function onClose($sid, $connection) {
        echo sprintf("close[%s]>>%s[%d]\n", $sid, $connection->ip, $connection->port);
    }
}
```

Document
----
* [Document 中文]()
* Document English. Wait moment.

License
-----
Apache License Version 2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>