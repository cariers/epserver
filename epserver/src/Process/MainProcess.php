<?php

namespace EPS\Process;

class MainProcess extends ChildProcess
{
    public static function instance($porcessName = 'epserver', $restart = true)
    {
        return new static($porcessName, $restart);
    }

    public function __construct($porcessName = 'epserver', $restart = true)
    {
        $this->porcessName = $porcessName;
        $this->restart     = false;
        $this->isMain = true;
    }
}