<?php

namespace EPS\Process\Event;

use EPS\Event\Loop;

class MainLoop
{
    public static function instance($process)
    {
        return Loop::addScTimer(function() use ($process) {
            if (method_exists($process->worker, 'loop')) {
                $process->worker->loop();
            }
        }, 1);
    }
}