<?php

namespace Server\Logic;

use EPS\Standard\Debug;

class Broadcast
{
    public function onData($data, $serv)
    {
        Debug::info('Broadcast %s', $data);
    }
}