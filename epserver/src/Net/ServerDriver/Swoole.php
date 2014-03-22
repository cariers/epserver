<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net\ServerDriver;

use EPS\Event\Emitter;

/**
 * Swoole驱动
 * @category EPS
 * @package Net
 * @author Evenlaz <evenlaz@gmail.com>
 */
class Swoole extends Emitter implements ServerDirverInterface
{
    protected $boardcastFilter = null;
    protected $dataFilter = null;
    protected $getSendDataHook = null;

    public static function instance($option)
    {
        return new static($option);
    }

    public function __construct($option)
    {
        $this->option = $option;
    }

    public function start()
    {
        $this->setTitle('proxy');
        $type = $this->option['type'] == 'UDP' ? SWOOLE_SOCK_UDP : SWOOLE_SOCK_TCP;
        $server = new \swoole_server(
            $this->option['host'],
            $this->option['port'],
            SWOOLE_PROCESS,
            $type
        );
        $server->set($this->option['setting']);
        $event = [
            'Start','Receive', 'Close', 'Shutdown',
            'Timer', 'WorkerStart', 'WorkerStop',
            'Task', 'Finish', 'WorkerError', 'MasterConnect',
            'MasterClose', 'Connect'
        ];
        foreach ($event as $event) {
            $server->on($event, [$this, sprintf('on%s', $event)]);
        }
        $this->server = $server;
        $this->server->start();
    }

    public function setBoardcastFilter(Callable $boardcastFilter)
    {
        $this->boardcastFilter = $boardcastFilter;
        return $this;
    }

    public function setDataFilter(Callable $dataFilter)
    {
        $this->dataFilter = $dataFilter;
        return $this;
    }

    public function setGetSendData(Callable $getSendDataHook)
    {
        $this->getSendDataHook = $getSendDataHook;
        return $this;
    }

    protected function boardcast($serv, $data, $flag = 0)
    {
        $startFd = 0;
        while(true) {
            $connList = $serv->connection_list($startFd, 100);
            if($connList === false) {
                break;
            }
            $startFd = end($connList);
            foreach($connList as $sid) {
                if ($flag && $this->boardcastFilter) {
                    $res = call_user_func_array($this->boardcastFilter, [$sid, $flag]);
                    $res and $serv->send($sid, $data);
                } else {
                    $serv->send($sid, $data);
                }
            }
        }
    }

    protected function send($serv, $data, $sid)
    {
        $serv->send($sid, $data);
    }

    public function onStart($serv)
    {
        $this->setTitle('reactor');
        $msg = sprintf(
            'master>>%d Manager>>%d ver>>%s',
            $serv->master_pid,
            $serv->manager_pid,
            SWOOLE_VERSION);
        $this->emit('Status', ['Server.Start', $msg]);
    }

    public function onConnect($serv, $fd, $fromId)
    {
        $info = $serv->connection_info($fd);
        $connection = new \StdClass;
        $connection->port = $info['remote_port'];
        $connection->ip = $info['remote_ip'];
        $this->emit('Connect', [$fd, $connection]);
    }

    public function onReceive($serv, $fd, $fromId, $data)
    {
        $this->emit('Receive', [$fd, $data]);
    }

    public function onClose($serv, $fd, $fromId)
    {
        $info = $serv->connection_info($fd);
        $connection = new \StdClass;
        $connection->port = $info['remote_port'];
        $connection->ip = $info['remote_ip'];
        $this->emit('Close', [$fd, $connection]);
    }

    public function onShutdown($serv)
    {
        $msg = sprintf(
            'master>>%d Manager>>%d',
            $serv->master_pid,
            $serv->manager_pid,
            SWOOLE_VERSION);
        $this->emit('Status', ['Server.Shutdown', $msg]);
    }

    public function onTimer($serv, $interval) {}
    public function onWorkerStart($serv, $workerId) {
        $msg = sprintf(
            'pid>>%d master>>%d Manager>>%d WorkerId>>%d',
            posix_getpid(),
            $serv->master_pid,
            $serv->manager_pid,
            $workerId);
        if ($workerId < $serv->setting['worker_num']) {
            $this->setTitle('worker');
            $this->emit('Status', ['Server.WorkerStart', $msg]);
        } else {
            $this->setTitle('task');
            //用于启动
            $taskWorkerNum = $workerId - $serv->setting['worker_num'];
            $serv->task('START', $taskWorkerNum);//用于启动TASK
            $this->emit('Status', ['Server.TaskWorkerStart', $msg]);
        }
    }

    public function onWorkerStop($serv, $workerId) {
        $msg = sprintf(
            'pid>>%d master>>%d Manager>>%d WorkerId>>%d',
            posix_getpid(),
            $serv->master_pid,
            $serv->manager_pid,
            $workerId);
        $this->emit('Status', ['Server.WorkerStop', $msg]);
    }

    public function onFinish($serv, $data) {

    }
    public function onWorkerError($serv, $workerId, $workerPid, $exitCode) {
        $msg = sprintf(
            'WorkerId>>%d Pid>>%d ExitCode>>%d',
            $workerId,
            $workerPid,
            $exitCode);
        if ($workerId < $serv->setting['worker_num']) {
            $this->emit('Status', ['Server.WorkerError', $msg]);
        } else {
            $this->emit('Status', ['Server.TaskWorkerError', $msg]);
        }
    }
    public function onMasterConnect($serv, $fd, $fromId) {}
    public function onMasterClose($serv, $fd, $fromId) {}

    public function onTask($serv, $taskId, $fromId, $taskType)
    {
        while (true) {
            $sendData = $this->getSendData();
            list($type, $flag, $data) = explode('@', $sendData, 3);
            if ($this->dataFilter) {
                $data = call_user_func_array($this->dataFilter, [$data]);
            }
            if ($data) {
                switch ($type) {
                    case static::TASK_BOARDCAST:
                        $this->boardcast($serv, $data, $flag);
                        break;
                    case static::TASK_SEND:
                        $this->send($serv, $data, $flag);
                        break;
                    case static::TASK_CLOSE:
                        $serv->close($flag);
                        break;
                    default:
                        //无法识别
                        break;
                }
            }
        }
    }

    public function getSendData()
    {
        return call_user_func_array($this->getSendDataHook, []);
    }

    public function setTitle($flag = 'proxy')
    {
        $title = cli_get_process_title();
        $titles = explode(' --sw', $title, 2);
        $title = sprintf('%s --sw-%s', $titles[0], $flag);
        cli_set_process_title($title);
    }
}
