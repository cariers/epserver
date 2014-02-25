<?php
define('SERVER_PATH', __DIR__ . '/');
$loader = include SERVER_PATH . '../autoload.php';

echo microtime(true) . '>>';
class Foo
{
    static $i = 0;
    public static function add()
    {
        static::$i ++;
        if (static::$i > 100000) {
            echo microtime(true);
            exit;
        }
    }
}
// register_tick_function('Foo::add');
// declare(ticks=1);
while (true) {
    usleep();
}
