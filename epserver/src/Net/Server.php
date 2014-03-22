<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net;

use EPS\Event\Emitter;

/**
 * 服务启动器
 * @category EPS
 * @package Net
 * @author Evenlaz <evenlaz@gmail.com>
 */
class Server extends Emitter
{
    const TASK_BOARDCAST = 'TASK_BOARDCAST';
    const TASK_SEND = 'TASK_SEND';
    const TASK_CLOSE = 'TASK_CLOSE';
    const TASK_ACCEPT = 'TASK_ACCEPT';
    const TASK_RECEIVE = 'TASK_RECEIVE';
    protected $driver = null;
    public static function instance($option, DispatcherInterface $dispatcher)
    {
        return new static($option, $dispatcher);
    }

    public function __construct($option, DispatcherInterface $dispatcher)
    {
        $class = $option['driver'] ? $option['driver'] : 'EPS\Net\ServerDriver\Swoole';
        $this->driver = new $class($option);
        $this->driver->on('Connect', function($sid, $connection) use ($dispatcher) {
            $dispatcher->onConnect($sid, $connection);
        })->on('Receive', function($sid, $data) use ($dispatcher) {
            $dispatcher->onReceive($sid, $data);
        })->on('Close', function($sid, $connection) use ($dispatcher) {
            $dispatcher->onClose($sid, $connection);
        })->setGetSendData(function() use ($dispatcher) {
            return $dispatcher->getSendData();
        });
    }

    public function setBoardcastFilter(Callable $boardcastFilter)
    {
        $this->driver->setBoardcastFilter($boardcastFilter);
        return $this;
    }

    public function setDataFilter(Callable $dataFilter)
    {
        $this->driver->setDataFilter($dataFilter);
        return $this;
    }

    public function start() {
        $this->driver->start();
    }

    public function getSendData() {
        return $this->dispatcher->getSendData();
    }
}