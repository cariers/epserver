<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Event;

/**
 * 事件接口
 * @package EPS
 * @category Event
 */
interface EmitterInterface
{
    /**
     * 监听
     * @param  string   $event
     * @param  callable $listener
     */
    public function on($event, callable $listener);

    /**
     * 触发一次
     * @param  string   $event
     * @param  callable $listener
     */
    public function once($event, callable $listener);

    /**
     * 移除监听
     * @param  string   $event
     * @param  callable $listener
     */
    public function removeListener($event, callable $listener);

    /**
     * 移除所有监听
     * @param  string $event
     */
    public function removeAllListeners($event = null);

    /**
     * 获取所有监听
     * @param  string $event
     * @return array
     */
    public function listeners($event);

    /**
     * 反射事件
     * @param  string $event
     * @param  mixed $arguments
     */
    public function emit($event, array $arguments = []);
}