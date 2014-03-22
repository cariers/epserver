<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Bootstrap;

use EPS\Process\Child as ChildProcess;

/**
 * 多进程服务
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class MultiProcessServer
{
    public static function instance($name = 'epserver', $daemon = false)
    {
        return new static($name, $daemon);
    }

    public function __construct($name = 'epserver', $daemon = false)
    {
        $this->name = $name;
        $this->daemon = $daemon;
    }

    public function setServerDispatcherOption($option = [])
    {
        $this->dispatcherOption = $option;
        return $this;
    }

    public function setServerOption($option = [])
    {
        $this->serverOption = $option;
        return $this;
    }

    public function setLogicOption($option = [])
    {
        $this->logicOption = $option;
        return $this;
    }

    public function run()
    {
        $option = [
            'name' => $this->name,
            'worker' => 'EPS\\Bootstrap\\MultiProcessWorker\\Main',
            'param' => [
                'dispatcherOption' => $this->dispatcherOption,
                'serverOption' => $this->serverOption,
                'logicOption'  => $this->logicOption
            ]
        ];
        if ($this->daemon) {
            ChildProcess::instance($option)->daemon(true);
        } else {
            ChildProcess::instance($option)->run(true);
        }
        // return $this;
    }
}