<?php

namespace Server\Process;

use EPS\Net\Server;
use EPS\Standard\GlobalShard;
use EPS\Standard\Message;
use EPS\Process\WorkerProcess;

use Server\Logic\Connection;
use Server\Logic\Broadcast;

class Main
{
    protected $process;

    public function __construct()
    {

    }

    public function start()
    {
        $servers = $this->createListen([5501, 5502]);
        GlobalShard::forever('Gateways', $servers);
        $pid = (700000 + posix_getpid()) * 1000;
        GlobalShard::forever('GatewayRecvMsg', new Message($pid + 1));
        GlobalShard::forever('GatewaySendMsg', new Message($pid + 2));
        GlobalShard::forever('SksRecvMsg', new Message($pid + 3));
        GlobalShard::forever('SksSendMsg', new Message($pid + 4));

        //启动SKS进程
        foreach ($servers as $server) {
            WorkerProcess::instance('eps_gateway')
            ->setWorker('Server\\Process\\Gateway', [$server])
            ->run();
        }
    }

    protected function createListen($listens = [])
    {
        $servers = [];
        $broadcast = new Broadcast;
        $connection = new Connection;
        foreach ($listens as $port) {
            $server = new Server();
            $server->listen($port);
            $server->openBroadcast(3);
            $server->on('Broadcast', [$broadcast, 'onData']);
            $server->on('Connect', [$connection, 'onConnect']);
            $server->on('Data', [$connection, 'onData']);
            $server->on('Close', [$connection, 'onClose']);
            $server->on('Start', function(){
                WorkerProcess::instance('eps_sks')
                    ->setWorker('Server\\Process\\SKSClient', ['0.0.0.0', 5601])
                    ->run();
            });
            $server->init();
            $serverId = $server->getId();
            $servers[$serverId] = $server;
        }
        return $servers;
    }
}