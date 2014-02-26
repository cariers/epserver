<?php

namespace Server\Logic;

use EPS\Standard\Debug;
use EPS\Standard\GlobalShard;

class Connection
{
    public function onConnect($guid, $info)
    {
        Debug::info('client connect %s -> %s', $guid, json_encode($info));
    }

    public function onClose($guid)
    {
        Debug::info('client close %s', $guid);
    }

    public function onData($guid, $data, $serv)
    {
        //写入到要读写的
        $data = str_replace(["\n", "\r"], '', $data);
        Debug::info('client data %s:%s', $guid, $data);
        GlobalShard::get('GatewayRecvMsg')->send($data);
        //$serv->send($guid, intval($data) + 1);
    }
}