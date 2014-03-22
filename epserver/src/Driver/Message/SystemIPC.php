<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Driver\Message;

/**
 * 分发器
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class SystemIPC implements MessageInterface
{

    public function __construct($message, $option = [])
    {
        $this->id = crc32($message);
        $this->perms = $option['perms'] ? $option['perms'] : 0666;
        if ($option['reset']) {
            $msg = msg_get_queue($this->id, $this->perms);
            $msg && msg_remove_queue($msg);
        }
        $this->msg = msg_get_queue($this->id, $this->perms);
    }

    public function send($data, $block = false, $serialize = false)
    {
        $error = 0;
        $type = 1;
        $r = msg_send($this->msg, $type, $data, $serialize, $block, $error);
        if (!$r) {

        }
        return $r;
    }
    public function receive($block = false, $serialize = false)
    {
        $data = [];
        $error = 0;
        $type = 1;
        $block = $block ? MSG_NOERROR : MSG_IPC_NOWAIT;
        $res = msg_receive($this->msg, $type, $type, 1024, $data, $serialize, $block, $error);
        return $res ? $data : null;
    }
}