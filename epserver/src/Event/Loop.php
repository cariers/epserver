<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Event;

use Libev\EventLoop;
use Libev\TimerEvent;
use Libev\ChildEvent;

/**
 * 事件接口
 * @package EPS
 * @category Event
 */
class Loop
{
    public static $events = [];

    public static function addUsTimer($callback, $interval = 100)
    {
        $event = new TimerEvent($callback, 0., $interval / 1000000);
        self::$events[] = $event;
        return $event;
    }

    public static function addScTimer($callback, $interval = 100)
    {
        $event = new TimerEvent($callback, 0., $interval);
        self::$events[] = $event;
        return $event;
    }

    public static function addMsTimer($callback, $interval = 100)
    {
        $event = new TimerEvent($callback, 0., $interval / 1000);
        self::$events[] = $event;
        return $event;
    }

    public static function addChildSig($callback, $pid)
    {
        $event = new ChildEvent($callback, $pid);
        self::$events[] = $event;
        return $event;
    }

    public static function run()
    {
        $loop = new EventLoop();
        foreach (self::$events as $events) {
            call_user_func_array([$loop, 'add'], [$events]);
        }
        $loop->run();
    }
}