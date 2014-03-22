<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Driver;

use EPS\Driver\Message\MessageInterface;

/**
 * 分发器
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class Message implements MessageInterface
{
    protected static $messages = [];
    public static function instance($driver, $message, $option = [])
    {
        $key = sprintf('%s-%s', $driver, $message);
        if (!isset(static::$messages[$key])) {
            $messageInstance = new static($driver, $message, $option);
            static::$messages[$key] = $messageInstance;
        }
        return static::$messages[$key];
    }

    public function __construct($driver, $message, $option = [])
    {
        $this->driver = new $driver($message, $option);
    }

    public function send($data, $block = false, $serialize = false)
    {
        return $this->driver->send($data, $block, $serialize);
    }
    public function receive($block = false, $serialize = false)
    {
        return $this->driver->receive($block, $serialize);
    }
}