<?php

namespace EPS\Worker;

use EPS\Net\Server;

class GatewayServer
{
    public function __construct($gateway)
    {
        $this->gateway = $gateway;
    }

    public function start()
    {
        //创建
        $server = Server::instance();
        $server->setServerMessage($this->gateway->serverMessage, false);
        $server->setSendMessage($this->gateway->sendMessage, false);
        $server->setBoardcastMessage($this->gateway->boardcastMessage, false);
        $server->listen($this->gateway->port, $this->gateway->host)->start();
    }
}