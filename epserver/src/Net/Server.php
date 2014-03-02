<?php

namespace EPS\Net;

use EPS\Standard\Debug;

class Server
{
    public $host = '0.0.0.0';
    public $port = 0;
    public $type = 'TCP';
    public $dispatchMode = 2;
    public $pollThreadNum = 2;
    public $writerNum = 4;
    public $workerNum = 8;
    public $boardcastWorkerNum = 2;
    public $sendWorkerNum = 2;

    protected $serverMessage = null;
    protected $sendMessage = null;
    protected $boardcastMessage = null;

    public static function instance()
    {
        return new static();
    }

    public function __construct()
    {

    }

    public function setServerMessage($message)
    {
        $this->serverMessage = $message;
        return $this;
    }

    public function setSendMessage($message)
    {
        $this->sendMessage = $message;
        return $this;
    }

    public function setBoardcastMessage($message)
    {
        $this->boardcastMessage = $message;
        return $this;
    }

    /**
     * 收到数据
     * @param  int    $id   连接ID
     * @param  string $data 收到的数据
     */
    public function onReceive($id, $data)
    {
        $data = ['sid' => $id, 'type' => 'Receive', 'data' => $data];
        $this->serverMessage->send($data, true, true);
    }

    /**
     * 客户端连接关闭
     * @param  int    $id   连接ID
     */
    public function onClose($id)
    {
        $data = ['sid' => $id, 'type' => 'Close'];
        $this->serverMessage->send($data, true, true);
    }

    /**
     * 客户端连接
     * @param  int         $id         连接ID
     * @param  Connection  $connection 连接对象
     */
    public function onConnect($id, Connection $info)
    {
        $data = ['sid' => $id, 'type' => 'Connect', 'data' => $info];
        $this->serverMessage->send($data, true, true);
    }

    /**
     * 客户端连接关闭
     * @param  int    $id   连接ID
     */
    public function onStatus($type, $msg)
    {
        Debug::info('[%10s] %s', $type, $msg);
        //$data = ['sid' => $id, 'type' => 'Close'];
        //$this->serverMessage->send($data, true, true);
    }

    public function getSendData()
    {
        return $this->sendMessage->receive(true, true);
    }

    /**
     * 广播数据
     * @param  int    $data 发送的数据
     * @param  int    $flag 标记
     */
    public function boardcast($data, $flag = 0)
    {
        return $this->boardcastMessage->receive(true, true);
    }

    public function getBoardcastData()
    {
        return $this->boardcastMessage->receive(true, true);
    }

    /**
     * 设置连接标记
     * @param int $id   连接ID
     * @param int $flag 标记
     */
    public function setConnectionFlags($id, $flag)
    {

    }

    /**
     * 获取连接标记
     * @param  int 连接ID
     * @return int 标记
     */
    public function getConnectionFlags($id)
    {

    }

    public function listen($port = 5501, $host = '0.0.0.0')
    {
        $this->port = $port;
        $this->host = $host;
        $this->server = SwooleServer::instance($this);
        $this->server->on('Status', [$this, 'onStatus']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Close', [$this, 'onClose']);
        return $this;
    }

    public function start()
    {
        $this->server->start();
    }
}