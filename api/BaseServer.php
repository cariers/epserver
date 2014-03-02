<?php
class StandardServer extends AbstractServer
{
    public static function instance()
    {
        return static();
    }

    /**
     * 收到数据
     * @param  int    $id   连接ID
     * @param  string $data 收到的数据
     */
    protected function onRecv($id, $data)
    {

    }

    /**
     * 客户端连接关闭
     * @param  int    $id   连接ID
     */
    protected function onClose($id)
    {

    }

    /**
     * 客户端连接
     * @param  int         $id         连接ID
     * @param  Connection  $connection 连接对象
     */
    protected function onConnect($id, Connection $info)
    {

    }

    /**
     * 发送数据
     * @param  int    $id   连接ID
     * @param  string $data 发送的数据
     */
    public function send($id, $data)
    {

    }

    /**
     * 广播数据
     * @param  int    $data 发送的数据
     * @param  int    $flag 标记
     */
    public function boardcast($data, $flag = 0)
    {

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
        $this->loop->start($this);
    }
}