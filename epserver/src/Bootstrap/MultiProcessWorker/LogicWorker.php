<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Bootstrap\MultiProcessWorker;

use EPS\Process\Child as ChildProcess;
use EPS\Event\Loop as EventLoop;
use EPS\Net\Server;
use EPS\ServerDispatcher\DispatchLogicInterface;

/**
 * 多进程服务
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class LogicWorker
{
    public function __construct()
    {
        $this->param = ChildProcess::current()->getWorkerParam();
        $this->dispatcher = $this->param['dispatcher'];
        $this->logic = new $this->param['dispatchLogic']($this->dispatcher);
        if (!$this->logic instanceof DispatchLogicInterface) {
            throw new \RuntimeException(sprintf('%s must instanceof EPS\\ServerDispatcher\\DispatcherInterface', $this->param['dispatchLogic']), 1);
        }
        $this->accept = $this->param['acceptMessage'];
        $this->receive = $this->param['receiveMessage'];
        //添加连接处理状态
        EventLoop::addUsTimer([$this, 'onAccept'], 100);
        EventLoop::addUsTimer([$this, 'onReceive'], 10);
    }

    public function start()
    {
        //启动Server
        //var_dump($this->param);
    }

    public function onAccept()
    {
        $data = $this->accept->receive(false, false);
        if ($data) {
            list($cmd, $sid, $connection) = explode('@', $data, 3);
            $connection = unserialize($connection);
            switch ($cmd) {
                case Server::TASK_ACCEPT:
                    $this->logic->onAccept($sid, $connection);
                    break;
                case Server::TASK_CLOSE:
                    $this->logic->onClose($sid, $connection);
                    break;
            }
        }
    }

    public function onReceive()
    {
        $data = $this->receive->receive(false, false);
        if ($data) {
            list($cmd, $sid, $data) = explode('@', $data, 3);
            $this->logic->onReceive($sid, $data);
        }
    }
}