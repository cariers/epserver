<?php

namespace EPS\Process\Event;

use EPS\Event\Loop;

class CheckParent
{
    public static function instance($process)
    {
        return Loop::addScTimer(function() use ($process) {
            if ($process->ppid !== posix_getppid()) {
                exit(0);
            }
        }, 1);
    }
}