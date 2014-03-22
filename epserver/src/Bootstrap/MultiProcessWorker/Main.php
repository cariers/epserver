<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Bootstrap\MultiProcessWorker;

use EPS\Process\Child as ChildProcess;
use EPS\Driver\Message;

/**
 * 多进程服务
 * 每个方法都会工作在不同进程下
 * @category EPS
 * @package ServerDispatcher
 * @author Evenlaz <evenlaz@gmail.com>
 */
class Main
{
    public function __construct()
    {
        $this->param = ChildProcess::current()->getWorkerParam();
    }

    public function start()
    {
        //启动Server
        $dispatcherOption = $this->param['dispatcherOption'];
        $accept = $dispatcherOption['acceptMessage'];
        $send = $dispatcherOption['sendMessage'];
        $receive = $dispatcherOption['receiveMessage'];
        $acceptMessage = Message::instance($accept['driver'], $accept['message'], $accept['option']);
        $receiveMessage = Message::instance($receive['driver'], $receive['message'], $receive['option']);
        $sendMessage = Message::instance($send['driver'], $send['message'], $send['option']);
        $dispatcher = $dispatcherOption['dispatcher'];
        $dispatcher = new $dispatcher($acceptMessage, $receiveMessage, $sendMessage);
        //启动Server进程
        $option = [
            'name' => ChildProcess::current()->getName(),
            'worker' => 'EPS\\Bootstrap\\MultiProcessWorker\\LogicWorker',
            'param' => [
                'acceptMessage' => $acceptMessage,
                'receiveMessage' => $receiveMessage,
                'sendMessage'  => $sendMessage,
                'dispatcher'   => $dispatcher,
                'dispatchLogic' => $this->param['logicOption']['dispatchLogic']
            ]
        ];
        $workerNums = isset($this->param['logicOption']['workerNum']) ? $this->param['logicOption']['workerNum'] : 1;
        for ($i=0; $i < $workerNums; $i++) {
            ChildProcess::instance($option)->run(false);
        }
        //启动Server进程
        $option = [
            'name' => ChildProcess::current()->getName(),
            'worker' => 'EPS\\Bootstrap\\MultiProcessWorker\\Server',
            'param' => [
                'acceptMessage' => $acceptMessage,
                'receiveMessage' => $receiveMessage,
                'sendMessage'  => $sendMessage,
                'dispatcherOption' => $dispatcherOption,
                'serverOption' => $this->param['serverOption'],
                'dispatcher'   => $dispatcher
            ]
        ];
        ChildProcess::instance($option)->run(false);

    }
}