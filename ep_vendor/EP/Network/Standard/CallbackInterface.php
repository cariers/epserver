<?php

namespace EP\Network\Standard;

interface CallbackInterface
{
    const CALLBACL_ON_CONNECT = 'connect';
    const CALLBACL_ON_CLOSE = 'close';
    const CALLBACL_ON_DATA = 'data';

    public function on($type, $callback);

    public function onConnect($callback);

    public function onClose($callback);

    public function onData($callback);

    public function callback($type, array $args);
}