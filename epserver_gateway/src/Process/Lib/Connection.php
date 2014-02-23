<?php

namespace Server\Process\Lib;

use EPS\Standard\Debug;

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
        //GlobalShard::get('MsgIdForGatewayRecv')->send($data);
        Debug::info('client data %s:%s', $guid, $data);
        $serv->send($guid, intval($data) + 1);
    }
}