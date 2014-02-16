<?php

namespace EP\Network\Standard;

abstract class AbstractCallback implements CallbackInterface
{
    protected $callbacks = [];

    public function on($type, $callback)
    {
        $this->callbacks[$type] = $callback;
        return $this;
    }

    public function onConnect($callback) {
        $this->on(static::CALLBACL_ON_CONNECT, $callback);
        return $this;
    }

    public function onClose($callback) {
        $this->on(static::CALLBACL_ON_CLOSE, $callback);
        return $this;
    }

    public function onData($callback) {
        $this->on(static::CALLBACL_ON_DATA, $callback);
        return $this;
    }

    public function callback($type, array $args)
    {
        if (isset($this->callbacks[$type])) {
            call_user_func_array($this->callbacks[$type], $args);
        } else {
            throw new \Exception(sprintf('EP\\Network\\Callback:%s no found!', $type), 1);
        }
    }
}