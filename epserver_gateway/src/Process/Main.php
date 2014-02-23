<?php

namespace Server\Process;

use EPS\Net\Server;
use EPS\Standard\GlobalShard;
use EPS\Standard\Message;
use EPS\Process\ChildProcess;

class Main
{
    protected $process;
    public function __construct($process)
    {
        $this->process = $process;
        $this->shardGateway();
        $this->shardMessage();

        $this->fork();
        while (true) {
            sleep(1);
        }
    }

    public function shardGateway()
    {
        $servers = [];
        $server = new Server();
        $server->listen(5503);
        $server->openAsyncBroadcast(3);
        $server->on('Broadcast', [new Lib\Broadcast, 'onData']);
        $server->on('Connect', [new Lib\Connection, 'onConnect']);
        $server->on('Data', [new Lib\Connection, 'onData']);
        $server->on('Close', [new Lib\Connection, 'onClose']);
        $server->init();
        $serverId = $server->getId();
        $servers[$serverId] = $server;

        $server = new Server();
        $server->listen(5504);
        $server->openAsyncBroadcast(3);
        $server->on('Broadcast', [new Lib\Broadcast, 'onData']);
        $server->on('Connect', [new Lib\Connection, 'onConnect']);
        $server->on('Data', [new Lib\Connection, 'onData']);
        $server->on('Close', [new Lib\Connection, 'onClose']);
        $server->init();
        $serverId = $server->getId();
        $servers[$serverId] = $server;
        //var_dump($servers);
        GlobalShard::forever('Gateways', $servers);
    }

    public function shardMessage()
    {
        $pid = (700000 + $this->process->pid) * 1000;
        GlobalShard::forever('MsgIdForGatewayRecv', new Message($pid + 1));
        GlobalShard::forever('MsgIdForGatewaySend', new Message($pid + 2));
        GlobalShard::forever('MsgIdForSKSRecv', new Message($pid + 3));
        GlobalShard::forever('MsgIdForSKSSend', new Message($pid + 4));
    }

    public function fork()
    {
        //创建监听Process
        foreach (GlobalShard::get('Gateways') as $server) {
            $child = new ChildProcess('Server\\Process\\Gateway', 'eps_gateway');
            $child->setParams([$server]);
            $child->fork();
        }
        //创建SKS连接进程
        $child = new ChildProcess('Server\\Process\\SKSClient', 'eps_sks_client');
    }
}