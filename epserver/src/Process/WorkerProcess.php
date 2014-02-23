<?php

namespace EPS\Process;

class WorkerProcess extends ChildProcess
{
    protected $timers = [];
    public function run()
    {
        declare(ticks=1);
        register_tick_function([$this, 'dispatcher']);
        while (true) {
            usleep(100);
        }
    }

    public function dispatcher()
    {
        $time = $this->uTime();
        foreach ($this->timers as $name => $timer) {
            $nextTouchTime = $timer['lastTime'] + $timer['interval'];
            if ($nextTouchTime < $time) {
                $this->touchTimer($name);
            }
        }
    }

    public function deleteTimer($name)
    {
        if (isset($this->timers[$name])) {
            unset($this->timers[$name]);
        }
        return $this;
    }

    public function touchTimer($name)
    {

        if (isset($this->timers[$name])) {
            $timer = &$this->timers[$name];
            $timer['lastTime'] = $this->uTime();
            call_user_func_array($timer['event'], $timer['param']);
        }
        return true;
    }

    public function addTimer($name, $interval, callable $callback, $param = [])
    {
        $this->timers[$name] = [
            'interval' => $interval / 1000000,
            'event' => $callback,
            'lastTime' => 0,
            'param' => $param
        ];
        return $this;
    }

    function uTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}