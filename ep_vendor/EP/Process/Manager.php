<?php

namespace EP\Process;

class Manager extends Base
{
    const CHILD_PROCESS_CATCH = 1;
    const CHILD_PROCESS_EXIT = 1;
    protected $childrenNamespaces = [];
    protected $childrens = [];
    protected $name = '';
    public function __construct($name)
    {
        $this->name = $name;
        $this->setName('manager');
        //注册信号
        pcntl_signal(SIGCHLD, [$this, 'sigHandler']);
    }

    //添加子进程
    public function addChildren($namespace, $nums = 1, $run = self::CHILD_PROCESS_EXIT)
    {
        $this->childrenNamespaces[$namespace] = [$nums, $run];
    }

    public function startChildren()
    {
        foreach ($this->childrenNamespaces as $namespace => $config) {
            for ($i=1; $i <= $config[0]; $i++) {
                $pid = $this->createChildren($namespace);
                $pid > 0 and $this->childrens[$pid] = $namespace;
            }
        }
    }

    public function run()
    {
        $this->startChildren();
        while (true) {
            //检测状态
            usleep(1000000);
        }
    }

    public function sigHandler($signo)
    {
        switch ($signo) {
            case SIGCHLD:
                $status = null;
                while (($pid = pcntl_waitpid(-1, $status, WNOHANG))> 0) {
                    $children = $this->childrens[$pid];
                    unset($this->childrens[$pid]);
                    $pid = $this->createChild($worker);
                    $pid > 0 and $this->childrens[$pid] = $namespace;
                }
                break;
        }
    }

    public function createChildren($namespace)
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            //进程创建失败
        } elseif ($pid > 0) {
            //进程创建成功, 分支执行
            $this->setName($namespace);
            new ChildProxy($namespace);
        }
        return $pid;
    }
}