<?php

namespace EPS\Process;

use EPS\Event\Emitter;

class MainProcess extends ChildProcess
{
    public function run()
    {
        //取队列
        $message = $this->worker->getMessage();
        //创建
        
    }
}