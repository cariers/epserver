<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net;

use EPS\Event\Emitter;
use EPS\Net\Event\Timeout;
/**
 * 事件接口
 * @package EPS
 * @category Event
 */
class Client extends Emitter
{
    protected $socket = null;
    public function __construct()
    {

    }

    public function connect($ip, $port, $type = self::TCP)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_nonblock($this->socket);
        $this->timeoutEvent = Timeout::instance($socket);
        socket_connect($this->socket, $ip, $port);
    }

    protected function setReadEvent()
    {
        $socket = $this->socket;
        $timeoutEvent = $this->timeoutEvent;
        $this->writeEvent = new EvIo($socket, Ev::WRITE, 
        function ($w) use ($socket, $timeoutEvent) {
            $timeoutEvent->stop();
            //分析有没有可写入的
            $this->emitWrite();
        });
        $this->readEvent = new EvIo($socket, Ev::READ, 
        function ($w) use ($socket) {
            $ret = socket_recv($socket, $out, 20, MSG_DONTWAIT);
            if ($ret) {
                $this->emitRead($ret);
            }
        });
    }

    public function close()
    {
        $this->timeoutEvent->stop();
        $this->readEvent->stop();
        $this->writeEvent->stop();
        socket_close($this->socket);
    }

    public function send($data)
    {
        socket_write($this->socket, $data, strlen($data));
    }

    public function emitWrite()
    {
        $this->emit('write', [$this]);
    }

    public function emitRead($data)
    {
        $this->emit('write', [$this. $data]);
    }
}