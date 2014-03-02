<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Service;

/**
 * 事件接口
 * @package EPS
 * @category Event
 */
class Service
{

    public static function __callStatic($method, $param = [])
    {
        //封装
        $protocol = (object) [];
        $protocol->method = $method;
        $protocol->
    }
}