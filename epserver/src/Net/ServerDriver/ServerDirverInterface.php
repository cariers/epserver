<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Net\ServerDriver;

/**
 * 服务驱动接口
 * @category EPS
 * @package Net
 * @author Evenlaz <evenlaz@gmail.com>
 */
interface ServerDirverInterface
{
    const TASK_BOARDCAST = 'TASK_BOARDCAST';
    const TASK_SEND = 'TASK_SEND';
    const TASK_CLOSE = 'TASK_CLOSE';
    public static function instance($option);
    public function setBoardcastFilter(Callable $boardcastFilter);
    public function setDataFilter(Callable $dataFilter);
    public function start();
}