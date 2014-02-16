<?php

namespace EP\Network\Server;
use EP\Network\Standard\AbstractServer;

class Tcp extends AbstractServer
{
    protected $type = 'TCP';
    public function __construct($port, $host = '0.0.0.0')
    {
        $this->port = $port;
        $this->host = $host;
    }
}