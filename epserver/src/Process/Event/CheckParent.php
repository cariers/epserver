<?php

namespace EPS\Process\Event;

class CheckParent
{
    public static function instance($process)
    {
        $ev = new \EvTimer(0., 1, function($w) use ($process){
            if ($process->ppid !== posix_getppid()) {
                exit(0);
            }
        });
        return $ev;
    }
}