<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net;

use EPS\Driver\Message;

/**
 * 分发器
 * @category EPS
 * @package Net
 * @author Evenlaz <evenlaz@gmail.com>
 */
interface DispatcherInterface
{
    public function __construct(Message $accept, Message $receive, Message $send);
    public function onConnect($sid, $connection);
    public function onReceive($sid, $data);
    public function onClose($sid, $connection);
    public function getSendData();
}