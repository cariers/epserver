<?php

namespace EPS\Worker;

use EPS\Net\Server;
use EPS\Process\WorkerProcess;
use EPS\Standard\Message;
use EPS\Event\Loop;

class GatewayDispatcher
{
    protected $gateway;
    public function __construct($gateway)
    {
        $this->gateway = $gateway;
    }

    public function start()
    {
        //注册消息处理
        Loop::addUsTimer([$this, 'onGatewayEvent'], 10);
    }

    public function onGatewayEvent()
    {
        $data = $this->gateway->serverMessage->receive(false, true);
        if ($data) {
            switch ($data['type']) {
                case 'Connect':
                    $this->gateway->logic->onConnect($data['sid'], $data['data']);
                    break;
                case 'Close':
                    $this->gateway->logic->onClose($data['sid']);
                    break;
                default:
                    $this->gateway->logic->onReceive($data['sid'], $data['data']);
                    break;
            }
        }
    }
}