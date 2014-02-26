<?php

namespace EPS\Process\Event;

class MainLoop
{
    public static function instance($process)
    {
        $ev = new \EvTimer(0., 1, function($w) use ($process){
            if (method_exists($process->worker, 'loop')) {
                $process->worker->loop();
            }
        });
        return $ev;
    }
}