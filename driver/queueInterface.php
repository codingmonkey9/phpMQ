<?php

//+++++++++++++++++++++++++++++++++++++++//
// PHPMessageQueue目录作为根目录
//+++++++++++++++++++++++++++++++++++++++//
namespace driver;

interface QueueInterface
{
	/**
     * 查询tubes（管道，即“队列”） 列表
     * 一个系统可能需要多个队列，比如短信队列，邮件队列
	 * @return array
	 */
	public function tubes(): array;

	/**
     * 向队列中存储一个消息任务job（向所有队列存储？）
     * @param Job $job
     * @return Job
     */
	public function put(Job $job):Job;

	/**
     * 从队列中接收一个消息任务
     * @param string $tube 指定从哪个队列接收消息任务
     * @return Job
     */
	public function reserve(string $tube): Job;

	/**
     * 删除某个消息任务(从所有队列中删除这个消息任务？)
     * @param Job $job
     * @return bool
     */
	public function delete(Job $job): bool;

	/**
     * 获取某个队列的消息任务列表
     * @param string $tube
     * @return array
     */
	public function jobs(string $tube): array;
}