<?php
/**
 * Created by PhpStorm.
 * User: huangshimin
 * Date: 2020/1/18
 * Time: 11:15
 */


// 自动加载
define('ROOT', realpath('./'));
spl_autoload_register(function($class) {
    $file_path = str_replace('\\', '/', $class);
    $file = $file_path. '.php';
    if (!file_exists($file)) {
        throw new Exception('err: not found file "'.$file.'"');
    }
    include $file;
    return true;
});