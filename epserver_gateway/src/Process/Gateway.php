<?php

namespace Server\Process;

class Gateway
{
    protected $server;
    public function __construct($server)
    {
        $this->server = $server;
    }

    public function start()
    {
        $this->server->start();
    }
}