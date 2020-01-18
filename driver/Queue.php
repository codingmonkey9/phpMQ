<?php
/**
 * Created by PhpStorm.
 * User: huangshimin
 * Date: 2020/1/17
 * Time: 11:37
 */

namespace driver;


class Queue
{
    public static $driver;

    /**
     * @param string $driver
     * @param array $options
     */
    public static function init($driver = 'MysqlDriver', $options = [])
    {
        $class = "driver\\$driver";
        if (self::$driver === null) {
            self::$driver = new $class($options);
        }
    }

    public static function tubes()
    {
        return self::$driver->tubes();
    }

    public static function put(Job $job)
    {
        return self::$driver->put($job);
    }

    public static function reserve($tube = 'default')
    {
        return self::$driver->reserve($tube);
    }

    public static function jobs($tube = 'default')
    {
        return self::$driver->jobs($tube);
    }

    public static function delete(Job $job)
    {
        return self::$driver->delete($job);
    }
}