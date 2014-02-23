<?php

namespace Server\Process;

use EPS\Standard\Debug;
use EPS\Standard\GlobalShard;

class Dispatcher
{
    protected $process;
    protected $client;
    public function __construct($process)
    {
        $process->addTimer('RecvFromRPCManager', 10, [$this, 'recv']);
    }

    public function recv()
    {
        $data = GlobalShard::get('MsgIdForRpcRecv')->recv();
        if ($data) {
            Debug::info('Work Data>>%s', $data);
            GlobalShard::get('MsgIdForRpcSend')->send(intval($data) + 1);
        }
    }
}