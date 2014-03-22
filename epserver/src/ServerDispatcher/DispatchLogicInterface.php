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
interface DispatchLogicInterface
{
    public function __construct(DispatcherInterface $dispatcher);
    public function onAccept($sid, $connection);
    public function onReceive($sid, $data);
    public function onClose($sid, $connection);
}