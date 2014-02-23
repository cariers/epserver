<?php

namespace Server\Process;

use EPS\Process\WorkerProcess;
use EPS\Net\Client;
use EPS\Standard\Debug;
use EPS\Standard\GlobalShard;
use EPS\Standard\Message;

class Main
{
    protected $process;
    protected $client;
    public function __construct($process)
    {
        $this->process = $process;
        //注册时间控制
        $this->client = new Client();
        $this->client->on('error', function($message) {
            Debug::info($message);
        });

        $this->client->connect('0.0.0.0', 5504);
        $this->client->send('1');

        $pid = (700000 + $this->process->pid) * 1000;

        GlobalShard::forever('MsgIdForRpcRecv', new Message($pid + 1));
        GlobalShard::forever('MsgIdForRpcSend', new Message($pid + 2));

        $process->addTimer('RecvFromRPCManager', 10, [$this, 'recv']);
        $process->addTimer('SendFromRPCManager', 10, [$this, 'send']);

        //Fork出子进程
        WorkerProcess::instance('Server\\Process\\Dispatcher', 'rpc_dispatcher')->fork();
    }

    public function recv()
    {
        $data = $this->client->recv();
        //写入到队列
        if ($data) {
            Debug::info('Read Data>>%s', $data);
            GlobalShard::get('MsgIdForRpcRecv')->send($data);
        }
    }

    public function send()
    {
        $data = GlobalShard::get('MsgIdForRpcSend')->recv();
        if ($data) {
            Debug::info('Send Data>>%s', $data);
            $this->client->send($data);
        }
    }
}