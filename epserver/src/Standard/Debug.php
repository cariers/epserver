<?php
namespace EPS\Standard;

//接收器
class Debug
{
    const DEBUG = 'DEUBG';
    const INFO = 'INFO';
    const EXCEP = 'EXCEP';

    public static function info()
    {
        $args = func_get_args();
        $msg = call_user_func_array('sprintf', $args);
        self::out($msg, static::INFO);
    }

    public static function excep()
    {
        $args = func_get_args();
        $msg = call_user_func_array('sprintf', $args);
        self::out($msg, static::EXCEP);
    }

    public static function debug()
    {
        $args = func_get_args();
        $msg = call_user_func_array('sprintf', $args);
        self::out($msg, static::DEBUG);
    }

    protected static function out($msg, $type)
    {
        $msg = sprintf('[%s][%5s][%05d]>>%s', date('Y-m-d H:i:s'), $type, posix_getpid(), $msg);
        //echo $msg . PHP_EOL;
        error_log($msg . PHP_EOL, 3, LOG_FILE);
    }
}