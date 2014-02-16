<?php

namespace EP\Network\Server;

class Manager
{
    protected $servers;
    protected $mode;
    public function __construct($mode = 'swoole')
    {
        $mode = sprintf('EP\\Network\\Server\\mode\\%s', $mode);
        $this->mode = new $mode;
    }

    public function add($server)
    {
        $this->servers[] = $server;
        $this->mode->addListener($server);
    }

    public function start()
    {
        $this->mode->start();
    }
}