<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\ServerDispatcher;

use EPS\Net\Server;
use EPS\Net\DispatcherInterface;
use EPS\Driver\Message;

/**
 * 分发器
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class MessageDispatcher implements DispatcherInterface
{
    public function __construct(Message $accept, Message $receive, Message $send)
    {
        $this->acceptMessage = $accept;
        $this->receiveMessage = $receive;
        $this->sendMessage = $send;
    }

    public function onConnect($sid, $connection)
    {
        $data = sprintf('%s@%s@%s', Server::TASK_ACCEPT, $sid, serialize($connection));
        $this->acceptMessage->send($data, false, false);
    }

    public function onReceive($sid, $data)
    {
        $data = sprintf('%s@%s@%s', Server::TASK_RECEIVE, $sid, $data);
        $this->receiveMessage->send($data, false, false);
    }

    public function onClose($sid, $connection)
    {
        $data = sprintf('%s@%s@%s', Server::TASK_CLOSE, $sid, serialize($connection));
        $this->acceptMessage->send($data, false, false);
    }

    public function getSendData()
    {
        return $this->sendMessage->receive(true, false);
    }

    public function send($sid, $data)
    {
        $data = sprintf('%s@%s@%s', Server::TASK_SEND, $sid, $data);
        $this->sendMessage->send($data, false, false);
    }

    public function close($sid)
    {
        $data = sprintf('%s@%s@1', Server::TASK_CLOSE, $sid);
        $this->sendMessage->send($data, false, false);
    }

    public function boardcast($data, $flag = 0)
    {
        $data = sprintf('%s@%s@%s', Server::TASK_BOARDCAST, $flag, $data);
        $this->sendMessage->send($data, false, false);
    }
}