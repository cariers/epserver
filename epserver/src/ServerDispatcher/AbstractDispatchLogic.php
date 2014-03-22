<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\ServerDispatcher;

use EPS\Net\DispatcherInterface;

/**
 * 多进程服务
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
abstract class AbstractDispatchLogic implements DispatchLogicInterface
{
    protected $dispatcher;
    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function onAccept($sid, $connection) {

    }
    public function onReceive($sid, $data) {

    }
    public function onClose($sid, $connection) {

    }

    public function send($sid, $data)
    {
        $this->dispatcher->send($sid, $data);
    }

    public function close($sid)
    {
        $this->dispatcher->close($sid);
    }

    public function boardcast($data, $flag = 0)
    {
        $this->dispatcher->boardcast($data, $flag);
    }
}