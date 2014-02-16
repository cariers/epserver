<?php
namespace EPServer\Process;
use EP\Process\Base;
use EP\Network\Server\Tcp as ServerTcp;
use EP\Network\Server\Manager as ServerManager;
class Reactor
{
    protected $serverManager;
    public function __construct($proxy)
    {
        $this->serverManager = new ServerManager('swoole');
        $server = new ServerTcp(9501);
        $server->on(ServerTcp::CALLBACL_ON_CONNECT, [$this, 'onConnect']);
        $server->on(ServerTcp::CALLBACL_ON_CLOSE, [$this, 'onClose']);
        $server->on(ServerTcp::CALLBACL_ON_DATA, [$this, 'onData']);
        $this->serverManager->add($server);
    }

    public function run()
    {
        $this->serverManager->start();
    }

    public function onConnect()
    {
        echo '>>client is connect';
    }

    public function onClose()
    {
        echo '>>client is Close';
    }

    public function onData()
    {
        echo '>>client is OnData';
    }
}