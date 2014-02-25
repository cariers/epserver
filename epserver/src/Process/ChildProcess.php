<?php

namespace EPS\Process;

use EPS\Event\ProcessCheckParent;
use EPS\Event\ProcessRestart;
use EPS\Standard\Emitter;

class ChildProcess extends Emitter
{
    public $workerName = '';
    public $porcessName = '';
    public $pid = 0;
    public $ppid = 0;
    protected $worker = null;
    protected $params = [];
    protected $restart = true;
    protected $isMain = false;
    protected $isDaemon = false;

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
        $this->init();
        //为自己时
        if ($this->isMain)
        {
            if ($this->isDaemon) {
                $this->fork(true);
            } else {
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
            \Ev::run();//没有事件时会退？？？
        } catch (\Exception $e){
            //异常
            echo $e->getMessage();
        }
    }

    protected function fork($parentExit = false)
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new \Exception(sprintf('%s fork fail', $this->porcessName), 1);
        } elseif ($pid === 0) {
            //父进程退出时自动自动关闭
            $this->isDaemon or ProcessCheckParent::instance($this);
            $this->runWorker();
        } else {
            if ($parentExit) {
                exit(0);
            } else {
                $this->pid = $pid;
                $this->ppid = posix_getpid();
                if ($this->restart) {
                    ProcessRestart::instance($this);
                }
                \Ev::run();
            }
        }
    }
}