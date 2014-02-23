<?php

namespace Server\Process\Lib;

class Broadcast
{
    public function onData($data, $serv)
    {
        Debug::info('Broadcast %s', $data);
    }
}