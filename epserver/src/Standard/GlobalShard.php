<?php

namespace EPS\Standard;

class GlobalShard
{
    protected static $values = [];
    public static function forever($key, $value = null)
    {
        if (!isset(static::$values[$key])) {
            static::$values[$key] = $value;
        }
    }

    public static function get($key, $default = null)
    {
        if (isset(static::$values[$key])) {
            $value = static::$values[$key];
        } else {
            $value = apc_fetch($key);
        }
        return $value ? $value : $default;
    }

    public static function set($key, $value = null)
    {
        return apc_store($key, $value);
    }
}