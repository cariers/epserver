<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net;

use EPS\Event\Emitter;
use EPS\Standard\Debug;
/**
 * 事件接口
 * @package EPS
 * @category Event
 */
class Server extends Emitter
{
    const TCP = 'TCP';
    const UDP = 'UDP';
    protected $listens = null;
    protected $isInit = false;
    protected $server = null;
    protected $openTask = false;
    protected $thisIndex = 0;
    protected static $index = 0;

    public function __construct()
    {
        static::$index ++;
        $this->thisIndex = static::$index;
    }

    public function getId()
    {
        return $this->thisIndex;
    }

    public function openAsyncBroadcast($nums)
    {
        $this->openTask = $nums;
        return $this;
    }

    public function listen($port, $host = '0.0.0.0', $type = self::TCP)
    {
        if (!in_array($type, [static::TCP, static::UDP])) {
            $error = sprintf('unknow server listen type:%s', $type);
            throw new \Exception($error, 1);
        }
        $this->listens[$port] = [$host, $type];
        return $this;
    }

    public function init()
    {
        if (!$this->isInit)
        {
            $config = [
                'worker_num' => 4,
                'writer_num' => 4,
                'poll_thread_num' => 4,
                'dispatch_mode' => 2
            ];
            if ($this->openTask) {
                $config['task_worker_num'] = $this->openTask;
            }
            foreach ($this->listens as $port => $host) {
                $type = $host[1] == 'UDP' ? SWOOLE_SOCK_UDP : SWOOLE_SOCK_TCP;
                if ($this->server === null) {
                    $this->server = new \swoole_server($host[0], $port, SWOOLE_PROCESS, $type);
                    //注册处理方法

                    $this->server->set($config);
                } else {
                    $this->server->addlistener($host[0], $port, $type);
                }
            }
            $this->isInit = true;
        }
        return;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function close($guid)
    {
        list($fd, $fromId) = explode('-', $guid);
        return $this->server->close($fd, $fromId);
    }

    public function broadcast($data)
    {
        return $this->server->task($data);
    }

    public function send($guid, $data)
    {
        list($fd, $fromId) = explode('-', $guid);
        return $this->server->send($fd, $data);
    }

    public function start()
    {
        $this->init();
        $this->server->on('Start', [&$this, 'onStart']);
        $this->server->on('WorkerStart', [&$this, 'onWorkerStart']);
        $this->server->on('Connect', [&$this, 'onConnect']);
        $this->server->on('Close', [&$this, 'onClose']);
        $this->server->on('Receive', [&$this, 'onReceive']);
        $this->server->on('WorkerStop', [&$this, 'onWorkerStop']);
        $this->server->on('WorkerError', [&$this, 'onWorkerError']);
        if ($this->openTask) {
            $this->server->on('Task', [&$this, 'onTask']);
            $this->server->on('Finish', [&$this, 'onFinish']);
        }
        $title = cli_get_process_title();
        cli_set_process_title($title . '->' . 'proxy');
        $this->server->start();
    }

    protected function error($type, $message = '')
    {
        $this->emit('Error', [$type, $message]);
    }

    public function onConnect($serv, $fd, $fromId)
    {
        $info = $serv->connection_info($fd);
        //转化
        $guid = sprintf('%d-%d', $fd, $this->thisIndex);
        $connection = [
            'guid' => $guid,
            'ip'   => $info['remote_ip'],
            'port' => $info['remote_port'],
            'from' => $this->thisIndex
        ];
        $this->emit('Connect', [$guid, $connection]);
    }

    public function onClose($serv, $fd, $fromId)
    {
        $guid = sprintf('%d-%d', $fd, $this->thisIndex);
        $this->emit('Close', [$guid]);
    }

    public function onReceive($serv, $fd, $fromId, $data)
    {
        $guid = sprintf('%d-%d', $fd, $this->thisIndex);
        $this->emit('Data', [$guid, $data, $this]);
    }

    public function onShutdown($serv)
    {
    }

    public function onFinish($serv, $task_id, $data)
    {

    }

    public function onWorkerStop($serv, $worker_id)
    {
        $this->emit('Status', ['WORKER_STOP', $worker_id]);
    }

    public function onStart($serv)
    {
        $title = cli_get_process_title();
        list($main, $last) = explode('->', $title);
        cli_set_process_title($main . '->' . 'reactor');
    }

    public function onWorkerStart($serv, $worker_id)
    {
        $title = cli_get_process_title();
        list($main, $last) = explode('->', $title);
        cli_set_process_title($main . '->' . 'worker_' . $worker_id);
    }

    public function onWorkerError($serv, $worker_id, $worker_pid, $exit_code)
    {
        $this->emit('Status', ['WORKER_ERROR', $worker_pid]);
    }

    public function onTask($serv, $fd, $fromId, $data)
    {
        $this->emit('Broadcast', [$data, $this]);
    }
}