<?php

namespace EPS\Process;

use EPS\Event\Emitter;

class MainProcess extends ChildProcess
{
    public function fork()
    {
        $this->init();
        $this->run();
        $this->emit('Fork.Success', [$this]);
        return $this->pid;
    }
}