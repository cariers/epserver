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
trait EmitterTrait
{
    /**
     * 监听列表
     * @var array
     */
    protected $listeners = [];

    public function on($event, callable $listener)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = $listener;
        return $this;
    }

    public function once($event, callable $listener) {
        $onceListener = function () use (&$onceListener, $event, $listener) {
            $this->removeListener($event, $onceListener);
            call_user_func_array($listener, func_get_args());
        };
        $this->on($event, $onceListener);
        return $this;
    }

    public function removeListener($event, callable $listener)
    {
        if (isset($this->listeners[$event])) {
            $index = array_search($listener, $this->listeners[$event], true);
            if (false !== $index) {
                unset($this->listeners[$event][$index]);
            }
        }
        return $this;
    }

    public function removeAllListeners($event = null)
    {
        if ($event !== null) {
            unset($this->listeners[$event]);
        } else {
            $this->listeners = [];
        }
        return $this;
    }

    public function listeners($event)
    {
        return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
    }

    public function emit($event, array $arguments = [])
    {
        foreach ($this->listeners($event) as $listener) {
            call_user_func_array($listener, $arguments);
        }
        return $this;
    }
}