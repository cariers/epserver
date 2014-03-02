<?php

namespace EPS\Process\Event;

use EPS\Event\Loop;
use EPS\Process\ChildProcess;


class Restart
{
    public static function instance($process)
    {
        return Loop::addScTimer(function() use ($process) {
            //检查子进程
            $status = null;
            while (($pid = pcntl_waitpid(-1, $status, WNOHANG))> 0) {
                ChildProcess::reStart($pid);
            }
        }, 1);
    }
}