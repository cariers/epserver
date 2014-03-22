<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Bootstrap\MultiProcessWorker;

use EPS\Process\Child as ChildProcess;
use EPS\Net\Server as ServerNet;

/**
 * 多进程服务
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class Server
{
    public function __construct()
    {
        $this->param = ChildProcess::current()->getWorkerParam();
    }

    public function start()
    {
        //启动Server
        $dispatcher = $this->param['dispatcher'];
        // //启动Server
        $serverOption = $this->param['serverOption'];
        ServerNet::instance($serverOption, $dispatcher)->start();
    }
}