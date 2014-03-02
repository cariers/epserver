<?php

class Swoole
{
    const START_BOARDCAST_KEY = 'START_BOARDCAST_KEY';
    public static function instance($server)
    {
        return static($server);
    }
    public function __construct($server)
    {
        $this->server = $server;
    }

    public function start()
    {
        $type = $this->server->type == 'UDP' ? SWOOLE_SOCK_UDP : SWOOLE_SOCK_TCP;
        $serv = new \swoole_server(
            $this->server->host,
            $this->server->port,
            SWOOLE_PROCESS,
            $type
        );
        $config = $this->getConfig();
        $serv->set($config);
        //BindEvent
        //$serv->on('Start', 'my_onStart');
        $event = [
            'Start','Receive', 'Close', 'Shutdown',
            'Timer', 'WorkerStart', 'WorkerStop',
            'Task', 'Finish', 'WorkerError', 'MasterConnect',
            'MasterClose', 'Connect'
        ];
        foreach ($event as $event) {
            $serv->on($event, [$this, sprintf('on%s', $event)]);
        }
        $this->server->start();
    }

    public function onStart($serv)
    {
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
        $connection = new Connection;
        $connection->port = $info['remote_port'];
        $connection->ip = $info['remote_ip'];
        $this->emit('Connect', [sprintf('%d-%d', $fd, $fromId), $connection]);
    }

    public function onReceive($serv, $fd, $fromId, $data)
    {
        $this->emit('Receive', [sprintf('%d-%d', $fd, $fromId)]);
    }

    public function onClose($serv, $fd, $fromId)
    {
        $this->emit('Close', [sprintf('%d-%d', $fd, $fromId)]);
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
            'master>>%d Manager>>%d WorkerId>>%d',
            $serv->master_pid,
            $serv->manager_pid,
            $workerId);
        if ($workerId < $serv->setting['worker_num']) {
            $this->resetProcessTitle('worker');
            $this->emit('Status', ['Server.WorkerStart', $msg]);
        } elseif ($workerId < $serv->setting['worker_num'] + $this->server->boardcastWorkerNum ) {
            $this->resetProcessTitle('boardcast');
            //开启广播
            $serv->task('StartBoardcast');
            $this->emit('Status', ['Server.BoardcastWorkerStart', $msg]);
        } else {
            $this->resetProcessTitle('task');
            $this->emit('Status', ['Server.TaskWorkerStart', $msg]);
        }
    }
    public function onWorkerStop($serv, $workerId) {}
    public function onTask($serv, $taskId, $fromId, $data) {
        if ($data === static::START_BOARDCAST_KEY) {
            while (true) {
                //$data = $this->server->getBoardcastData();
                usleep(100);
            }
        } else {
            $result = $this->server->onTask($data);
            if ($result) {
                $serv->finish($result);
            }
        }
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
        } elseif ($workerId < $serv->setting['worker_num'] + $this->server->boardcastWorkerNum ) {
            $this->emit('Status', ['Server.BoardcastWorkerError', $msg]);
        } else {
            $this->emit('Status', ['Server.TaskWorkerError', $msg]);
        }
    }
    public function onMasterConnect($serv, $fd, $fromId) {}
    public function onMasterClose($serv, $fd, $fromId) {}

    protected function getConfig()
    {
        $config = [];
        $config['dispatch_mode'] = $this->server->dispatchMode;
        $config['poll_thread_num'] = $this->server->pollThreadNum;
        $config['writer_num'] = $this->server->writerNum;
        $config['worker_num'] = $this->server->workerNum;
        //Task
        $taskWorkerNum = $this->server->taskWorkerNum + $this->server->boardcastWorkerNum;
        if ($taskWorkerNum > 0) {
            $config['task_worker_num'] = $taskWorkerNum;
        }
        return $config;
    }

    public function resetProcessTitle($flag = 'proxy')
    {
        $title = cli_get_process_title();
        $titles = explode(' --swoole', $title);
        $title = sprintf('%s --swoole-', $titles[0], $flag);
        cli_set_process_title($title);
    }
}