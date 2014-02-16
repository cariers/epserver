<?php

namespace EP\Process;

class ChildProxy extends Base
{
    protected $namespace;
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
        $this->run();
    }

    public function run()
    {
        $process = new $this->namespace($this);
        $process->run();
        exit(0);
    }
}