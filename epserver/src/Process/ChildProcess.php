<?php

namespace EPS\Process;

use EPS\Process\Event\CheckParent;
use EPS\Process\Event\Restart;
use EPS\Process\Event\MainLoop;
use EPS\Event\Emitter;
use EPS\Standard\Debug;
use EPS\Event\Loop;

class ChildProcess extends Emitter
{
    public $workerName = '';
    public $porcessName = '';
    public $pid = 0;
    public $ppid = 0;
    public $worker = null;
    protected $params = [];
    protected $restart = true;
    protected $isMain = false;
    protected $isDaemon = false;
    protected static $processLists = [];

    public static function instance($porcessName = 'epserver', $restart = true)
    {
        return new static($porcessName, $restart);
    }

    public function __construct($porcessName = 'epserver', $restart = true)
    {
        $this->porcessName = $porcessName;
        $this->restart     = $restart;
    }

    public function setWorker($worker, $param = [])
    {
        $this->workerName = $worker;
        $this->params = $param;
        return $this;
    }

    public function setDaemon()
    {
        $this->isDaemon = true;
        return $this;
    }

    public function setMain()
    {
        $this->isMain = true;
        return $this;
    }

    protected function init()
    {
        $this->pid = posix_getpid();
        $this->ppid = posix_getppid();
        cli_set_process_title(sprintf('%s(%s)', $this->porcessName, $this->workerName));
    }

    public function run()
    {
        //为自己时
        if ($this->isMain)
        {
            if ($this->isDaemon) {
                $this->fork(true);
            } else {
                $this->init();
                $this->runWorker();
            }
        } else {
            $this->fork(false);
        }
    }

    protected function runWorker()
    {
        try {
            $ref = new \ReflectionClass($this->workerName);
            $this->worker = $ref->newInstanceArgs($this->params);
            if (method_exists($this->worker, 'start')) {
                $this->worker->start();
            }
            //主进程实现轮询
            if ($this->isMain) {
                MainLoop::instance($this);
            }
            Loop::run();
        } catch (\Exception $e){
            //异常
            Debug::excep('runWorker error: %s', $e->getMessage());
        }
    }

    protected function fork($parentExit = false)
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new \Exception(sprintf('%s fork fail', $this->porcessName), 1);
        } elseif ($pid === 0) {
            //非主进程时，自动退出进程
            $this->init();
            $this->isMain or CheckParent::instance($this);
            $this->runWorker();
        } else {
            if ($parentExit) {
                exit(0);
            } else {
                $this->pid = $pid;
                $this->ppid = posix_getpid();
                if ($this->restart) {
                    Restart::instance($this);
                }
                static::$processLists[$this->pid] = $this;
                $this->isMain and Loop::run();
            }
        }
    }

    public static function reStart($pid)
    {
        $process = static::$processLists[$pid];
        unset(static::$processLists[$pid]);
        $process->run();
    }
}