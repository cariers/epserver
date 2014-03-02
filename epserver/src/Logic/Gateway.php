<?php

namespace EPS\Logic;

class Gateway
{

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function onConnect($sid, $connection)
    {

    }

    public function onClose($sid)
    {

    }

    public function onReceive($sid, $data)
    {

    }
}