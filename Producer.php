<?php
/**
 * Created by PhpStorm.
 * User: huangshimin
 * Date: 2020/1/18
 * Time: 11:21
 */

// mysql生产者
include_once 'boot.php';
try {
    \driver\Queue::init('MysqlDriver', [
        'dsn' => 'mysql:host=localhost;dbname=queue_test',
        'user' => 'root',
        'pass' => 'root',
        'table' => 'queues',
        'ttr' => 60,
    ]);  //队列初始化

    // 生产者放入消息任务
    $job = new \driver\Job([
        'job_data' => json_encode(['order_id' => time(), 'user_id' => 0001]),
        'tube' => 'test',
    ]);
    $job = \driver\Queue::put($job);
} catch (Exception $e) {
    print_r($e->getMessage().$e->getLine());
}