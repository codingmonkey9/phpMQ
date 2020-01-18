<?php
/**
 * Created by PhpStorm.
 * User: huangshimin
 * Date: 2020/1/18
 * Time: 11:34
 */

include_once 'boot.php';
try {
    \driver\Queue::init('MysqlDriver', [
        'dsn' => 'mysql:host=localhost;dbname=queue_test',
        'user' => 'root',
        'pass' => 'root',
        'table' => 'queues',
        'ttr' => 60
    ]);
//    while (1) {
        // 不断从消息队列中读取数据
        $job = \driver\Queue::reserve('test');
        if (!$job->isEmpty()) {
            echo $job->job_data.PHP_EOL;
            sleep(2);
            if (\driver\Queue::delete($job)) {
                echo 'job was deleted<br>';
            } else {
                echo 'delete failed<br>';
            }
        }
//    }
} catch (Exception $e) {
    print_r($e->getMessage());
}