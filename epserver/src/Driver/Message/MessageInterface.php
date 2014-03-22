<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Driver\Message;

/**
 * 消息接口
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
interface MessageInterface
{
    public function send($data, $block = false, $serialize = false);

    public function receive($block = false, $serialize = false);
}