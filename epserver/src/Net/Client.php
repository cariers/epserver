<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net;

use EPS\Event\Emitter;
/**
 * 事件接口
 * @package EPS
 * @category Event
 */
class Client extends Emitter
{
    const TCP = 'TCP';
    const UDP = 'UDP';
    public function __construct()
    {

    }

    public function connect($ip, $port, $type = self::TCP)
    {
        $type = $type === self::TCP ? SWOOLE_SOCK_TCP : SWOOLE_SOCK_UDP;
        $client = new \swoole_client($type, SWOOLE_SOCK_SYNC);
        $ret = $client->connect($ip, $port);
        if (!$ret) {
            $this->emit('error', [sprintf('Connect Serve %s[%d]', $ip, $port)]);
        } else {
            $this->client = $client;
        }
    }

    public function send($data)
    {
        $this->client->send($data);
    }

    public function recv()
    {
        $write = $error = array();
        $read = [$this->client];
        $n = swoole_client_select($read, $write, $error, 0.00006);
        if ($n > 0) {
            foreach ($read as $socket) {
                $data = $socket->recv();
                return $data;
            }
        }
        return null;
    }
}