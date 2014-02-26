<?php

namespace EPS\Process\Event;

class Restart
{
    public static function instance($process)
    {
        new \EvChild($process->pid, FALSE, function ($w, $revents) use ($process) {
            $w->stop();
            $process->run();
        });
    }
}