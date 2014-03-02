<?php

namespace EPS\Standard;

class Message
{
    protected $id;
    public function __construct($id, $perms = 0666, $removeLast = true)
    {
        $this->id = static::getIdByKey($id);
        $this->perms = $perms;
        if ($removeLast) {
            $msg = msg_get_queue($this->id, $this->perms);
            $msg && msg_remove_queue($msg);
        }
        $this->resource = msg_get_queue($this->id, $this->perms);
    }

    public function send($data, $blocking = true, $serialize = false)
    {
        $error = 0;
        $type = 1;
        $r = msg_send($this->resource, $type, $data, $serialize, $blocking, $error);
        if (!$r) {
            var_dump(msg_stat_queue($this->resource));
            exit;
        }
        return $r;
    }

    public function receive($blocking = false, $serialize = false)
    {
        $type = 0;
        $data = 0;

        $data = [];
        $error = 0;
        $type = 1;
        $blocking = $blocking ? MSG_NOERROR : MSG_IPC_NOWAIT;
        $res = msg_receive($this->resource, $type, $type, 1024, $data, $serialize, $blocking, $error);
        return $res ? $data : null;
    }

    public static function getIdByKey($key)
    {

        //有可能并发执行 TODO 进程信号控制
        $keys = apc_fetch('epserver_message_values');
        is_array($keys) or $keys = [];
        if (array_key_exists($key, $keys)) {
            return (int) $keys[$key];
        } else {
            apc_add('epserver_message_inc', 1);
            $id = 55000000 + apc_inc('epserver_message_inc');
            $keys['key'] = $id;
            apc_store('epserver_message_values', $keys);
            return $id;
        }
    }
}