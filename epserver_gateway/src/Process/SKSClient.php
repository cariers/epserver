<?php

namespace Server\Process;

use EPS\Standard\GlobalShard;
use EPS\Standard\Debug;

class SKSClient
{
    protected $host;
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        Debug::info('SKSClient __construct');
    }

    public function start()
    {
        Debug::info('SKSClient Start:');
        new \EvTimer(0., 1, function($w) {
            //读取
            $message = GlobalShard::get('GatewayRecvMsg')->recv();
            Debug::info('SKSClient message: %s', $message);
            if ($message) {
                //写到广播
                $servers = GlobalShard::get('Gateways');
                foreach ($servers as $id=>$server) {
                    Debug::info('SKSClient to broadcast: %d', $id);
                    $server->broadcast($message);
                }
            }
        });
    }
}