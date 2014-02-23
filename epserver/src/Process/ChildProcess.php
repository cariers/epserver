<?php

namespace EPS\Process;

use EPS\Event\Emitter;

class ChildProcess extends Emitter
{
    public $workerName = '';
    public $porcessName = '';
    public $pid = 0;
    public $ppid = 0;
    protected $worker = null;
    protected $params = [];

    public static function instance($workerName, $porcessName = 'epserver')
    {
        return new static($workerName, $porcessName);
    }

    public function __construct($workerName, $porcessName = 'epserver')
    {
        $this->workerName = $workerName;
        $this->porcessName = $porcessName;
    }

    public function setParams($param = [])
    {
        $this->params = $param;
    }

    public function init()
    {
        $this->pid = posix_getpid();
        $this->ppid = posix_getppid();
        cli_set_process_title(sprintf('%s(%s)', $this->porcessName, $this->workerName));
        $name = $this->workerName;
        $this->worker = new $name($this);
        if ($this->params && method_exists($this->worker, 'setParams')) {
            call_user_func_array([$this->worker, 'setParams'], $this->params);
        }
        return $this;
    }

    public function checkParentProcess()
    {
        $ppid = posix_getpid();
        if ($ppid != $this->ppid) {
            exit(0);
        }
        $this->ppid = $ppid;
    }

    public function run()
    {

    }

    public function fork()
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            $this->emit('Fork.Error', [$this]);
            return false;
        } elseif ($pid === 0) {
            $this->init();
            $this->run();
        } else {
            $this->ppid = posix_getpid();
            $this->pid = $pid;
        }
        $this->emit('Fork.Success', [$this]);
        return $pid;
    }
}