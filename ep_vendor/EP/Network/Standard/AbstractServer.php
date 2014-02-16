<?php

namespace EP\Network\Standard;

abstract class AbstractServer extends AbstractCallback
{
    protected $type = 'TCP';
    protected $port = 0;
    protected $host = '0.0.0.0';
    public function __construct($port, $host = '0.0.0.0')
    {
        $this->port = $port;
        $this->host = $host;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getHost()
    {
        return $this->host;
    }
}