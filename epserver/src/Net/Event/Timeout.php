<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net\Event;

/**
 * 事件接口
 * @package EPS
 * @category Event
 */
class Timeout
{
    public function instance($socket, $time = 3.0)
    {
        return new EvTimer($time, 0., function () use ($socket) {
            socket_close($socket);
            Ev::stop(Ev::BREAK_ALL);
        });
    }
}