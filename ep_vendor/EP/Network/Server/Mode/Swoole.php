<?php

namespace EP\Network\Server\Mode;
use EP\Network\Standard\AbstractCallback;
class Swoole
{
    protected $listeners = [];
    protected $connections = [];
    public function __construct()
    {

    }

    public function addListener($server)
    {
        $this->listeners[$server->getPort()] = $server;
    }

    public function onConnect($serv, $fd)
    {
        $info = $serv->connection_info($fd);
        $this->connections[$fd] = [];
        $this->connections[$fd]['info'] = $info;
        $this->connections[$fd]['server'] = &$this->listeners[$info['from_port']];
        $this->connections[$fd]['server']->callback(AbstractCallback::CALLBACL_ON_CONNECT, []);
    }

    public function onClose($serv, $fd)
    {
        $this->connections[$fd]['server']->callback(AbstractCallback::CALLBACL_ON_CLOSE, []);
        unset($this->connections[$fd]);
    }

    public function onData($serv, $fd, $from_id, $data)
    {
        $this->connections[$fd]['server']->callback(AbstractCallback::CALLBACL_ON_DATA, []);
    }

    public function start()
    {
        $serv = null;
        foreach ($this->listeners as $key => $server) {
            $host = $server->getHost();
            $port = $server->getPort();
            $type = $server->getType();
            $type = $type == 'UDP' ? SWOOLE_SOCK_UDP : SWOOLE_SOCK_TCP;
            if ($serv === null) {
                $serv = new \swoole_server($host, $port, $type);
            } else {
                $serv->addlistener($host, $port, $type);
            }
        }
        $serv->on('connect', [$this, 'onConnect']);
        $serv->on('close', [$this, 'onClose']);
        $serv->on('receive', [$this, 'onData']);
        $serv->start();
    }
}