<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace Server\Logic;

use EPS\ServerDispatcher\AbstractDispatchLogic;

/**
 * 多进程服务
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class DispatchLogic extends AbstractDispatchLogic
{
    public function onAccept($sid, $connection) {
        echo sprintf("accept[%s]>>%s[%d]\n", $sid, $connection->ip, $connection->port);
        $this->send($sid, 'hello ' . $sid . "\n");
        $this->boardcast($sid . ' join room ~' . "\n");
    }
    public function onReceive($sid, $data) {
        $data = str_replace(["\n", "\r"], '', $data);
        if ($data == 'close') {
            $this->boardcast($sid . ' logout' . "\n");
            $this->close($sid);
        } else {
            $this->send($sid, 'you say ' . $data . "\n");
        }
        echo sprintf("receive[%s]>>%s\n", $sid, $data);
    }
    public function onClose($sid, $connection) {
        echo sprintf("close[%s]>>%s[%d]\n", $sid, $connection->ip, $connection->port);
    }
}