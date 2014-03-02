<?php

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
        //转发逻辑
    }
}