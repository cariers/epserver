<?php

namespace Server\Worker;

use EPS\Process\WorkerProcess;

class Main
{
    protected $process;

    public function __construct()
    {

    }

    public function start()
    {
        WorkerProcess::instance('eps_gateway')
            ->setWorker('EPS\\Worker\\Gateway', ['Server\\Logic\\Gateway', 5501])
            ->run();
    }
}