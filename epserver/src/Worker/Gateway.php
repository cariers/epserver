<?php

namespace EPS\Worker;

use EPS\Net\Server;
use EPS\Process\WorkerProcess;
use EPS\Standard\Message;
use EPS\Event\Loop;

class Gateway
{
    public $serverMessage, $sendMessage, $boardcastMessage;
    public $port, $host, $logic;
    public function __construct($logic, $port = 5501, $host = '0.0.0.0')
    {
        $this->logic = new $logic($this);
        $this->port = $port;
        $this->host = $host;
        $this->serverMessage = new Message(sprintf('GatewayServer_%d', $port));
        $this->sendMessage = new Message(sprintf('GatewaySend_%d', $port));
        $this->boardcastMessage = new Message(sprintf('GatewayBoardcast_%d', $port));
    }

    public function send($sid, $data)
    {
        $data = [
            'sid' => $sid,
            'data' => $data
        ];
        $a = $this->sendMessage->send($data, false, true);
    }

    public function boardcast($data, $flag = 0)
    {
        $data = [
            'flag' => $flag,
            'data' => $data
        ];
        $this->boardcastMessage->send($data, false, true);
    }

    public function start()
    {
        //创建
        WorkerProcess::instance('eps_gateway_server')
            ->setWorker('EPS\\Worker\\GatewayServer', [$this])
            ->run();
        //注册消息处理
        WorkerProcess::instance('eps_gateway_dispatcher')
            ->setWorker('EPS\\Worker\\GatewayDispatcher', [$this])
            ->run();
    }
}