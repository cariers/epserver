服务开发框架 epserver
=====

* 事件驱动
* 进程管理
* 多种服务

Example
-----

__主进程__
```php
use EPS\Process\MainProcess;
define('SERVER_PATH', __DIR__ . '/');
define('LOG_FILE', __DIR__ . '/ttt.log');
$loader = include SERVER_PATH . '../autoload.php';
MainProcess::instance('eps_main')
    ->setWorker('Server\\Worker\\Main')
    ->setMain()
    //->setDaemon()
    ->run();
```

__启动网关服务__
```php
namespace Server\Worker;
use EPS\Process\WorkerProcess;
class Main
{
    public function __construct()
    {
    }
    public function start()
    {
        //启动网关进程
        WorkerProcess::instance('eps_gateway')
            ->setWorker('EPS\\Worker\\Gateway', ['Server\\Logic\\Gateway', 5501])
            ->run();
    }
}
```

__网关逻辑__
```php
namespace Server\Logic;
use EPS\Standard\Debug;
class Gateway
{
    public function __construct($server)
    {
        $this->server = $server;
    }
    public function onConnect($sid, $connection)
    {
        Debug::info('Client Connect %s >> %s[%d]', $sid, $connection->ip, $connection->port);
        $this->server->send($sid, 'hello epserver!' . PHP_EOL);
    }
    public function onClose($sid)
    {
        Debug::info('Client Close %s', $sid);
    }
    public function onReceive($sid, $data)
    {
        $data = str_replace(["\n", "\r"], '', $data);
        Debug::info('Client OnData %s >> %s', $sid, $data);
        if (strrpos($data, 'boardcast') !== false) {
            $this->server->boardcast($data);
        } else {
            $this->server->send($sid, 'you say:'. $data . PHP_EOL);
        }
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