<?php
/**
 * 简单服务开发框架>>EPS
 * (c) Evenlaz <evenlaz@gmail.com>
 */

namespace EPS\Event;

/**
 * 事件接口
 * @package EPS
 * @category Event
 */
class Emitter implements EmitterInterface
{
    use EmitterTrait;
}