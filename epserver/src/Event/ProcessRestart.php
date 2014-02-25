<?php

namespace EPS\Event;

class ProcessCheckParent
{
    public static function instance($process)
    {
        $w = new EvChild($pid, FALSE, function ($w, $revents) use ($process) {
            $w->stop();
            $process->fork();
        });
    }
}