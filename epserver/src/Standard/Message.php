<?php

namespace EPS\Standard;

class Message
{
    protected $id;
    public function __construct($id, $perms = 0666, $removeLast = true)
    {
        $this->id = $id;
        $this->perms = $perms;
        if ($removeLast) {
            $msg = msg_get_queue($this->id, $this->perms);
            $msg && msg_remove_queue($msg);
        }
        $this->resource = msg_get_queue($this->id, $this->perms);
    }

    public function send($data, $blocking = false)
    {
        $error = 0;
        $type = 1;
        $r = msg_send($this->resource, $type, $data, false, $blocking, $error);
        return $r;
    }

    public function recv($blocking = false)
    {
        $type = 0;
        $data = 0;

        $data = [];
        $error = 0;
        $type = 1;
        $blocking = $blocking ? MSG_NOERROR : MSG_IPC_NOWAIT;
        $res = msg_receive($this->resource, $type, $type, 1024, $data, false, $blocking, $error);
        return $res ? $data : null;
    }
}