<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Process;

use EPS\Event\Emitter;
use EPS\Event\Loop as EventLoop;

/**
 * 子进程
 * @category EPS
 * @package Process
 * @author Evenlaz <evenlaz@gmail.com>
 */
class Child extends Emitter
{
    protected static $processes = [];
    protected static $currentProcess = null;
    protected $option;
    public $pid;
    public $ppid;
    public $worker;
    public $isMain = false;
    public $isDaemon = false;
    public static function instance($option = [])
    {
        $process = new static($option);
        $processes[] = $process;
        return $process;
    }

    public static function current()
    {
        return static::$currentProcess;
    }

    public function __construct($option = [])
    {
        $this->option = $option;
    }

    protected function init()
    {
        $this->pid = posix_getpid();
        $this->ppid = posix_getppid();
        cli_set_process_title(sprintf('%s(%s)', $this->getName(), $this->getWorker()));
        static::$currentProcess = $this;
        return $this;
    }

    protected function runWorker()
    {
        $worker = $this->getWorker();
        $this->worker = new $worker;
        if (method_exists($this->worker, 'start')) {
            $this->worker->start();
        }
        if ($this->isMain) {
            //添加定时Worker通知（10S）
            $process = $this;
            EventLoop::addScTimer(function() use ($process) {
                if (method_exists($process->worker, 'loop')) {
                    $process->worker->loop();
                }
            }, 10);
        }
        EventLoop::run();
    }

    public function run($main = false)
    {
        $this->isMain = $main;
        if ($this->isMain && ! $this->isDaemon) {
            $this->init();
            $this->runWorker();
        } else {
            $this->fork();
        }
    }

    public function daemon($main = false)
    {
        $this->isDaemon = true;
        $this->run($main);
    }

    protected function fork()
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            $this->emit('error', sprintf('%s fork fail!', $this->getWorker()));
        } elseif ($pid === 0) {
            $this->init();
            if (!$this->isMain) {
                //添加父进程退出检测(自动退出子进程)
                $process = $this;
                EventLoop::addScTimer(function() use ($process) {
                    if ($process->ppid !== posix_getppid()) {
                        exit(0);
                    }
                }, 1);
            }
            $this->runWorker();
        } else {
            $this->pid = $pid;
            $this->ppid = posix_getpid();
            if ($this->isMain && $this->isDaemon) {
                exit(0);
            }
            if ($this->isReboot()) {
                $process = $this;
                EventLoop::addScTimer(function() use ($process) {
                    //检查子进程
                    $status = null;
                    while (($pid = pcntl_waitpid(-1, $status, WNOHANG))> 0) {
                        $process->restart($pid);
                    }
                }, 1);
            }
            $this->isMain and EventLoop::run();
        }
        return $pid;
    }

    public function isReboot()
    {
        return isset($this->option['noReboot']) && $this->option['noReboot'] ? false : true;
    }

    public function restart($pid)
    {
        foreach (static::$processes as $process) {
            if ($process->pid == $pid) {
                $process->run(false);
            }
        }
    }

    public function getName()
    {
        return $this->option['name'];
    }

    public function getWorker()
    {
        return $this->option['worker'];
    }

    public function getWorkerParam()
    {
        return $this->option['param'];
    }

    public function getConfig()
    {
        return $this->option['config'];
    }
}