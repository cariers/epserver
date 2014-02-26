<?php

namespace EPS\Process;

class WorkerProcess extends ChildProcess
{
    public static function instance($porcessName = 'epserver', $restart = true)
    {
        return new static($porcessName, $restart);
    }

    public function __construct($porcessName = 'epserver', $restart = true)
    {
        $this->porcessName = $porcessName;
        $this->restart     = true;
        $this->isMain = false;
    }
}