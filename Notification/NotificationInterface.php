<?php
namespace Message\Notification;

use Message\Report\NotificationReport;

/**
 * 외부 메시지 전송 패키지 - Notification
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
interface NotificationInterface
{
    /**
     * Set the task config.
     *
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * @return array
     */
    public function getConfig();
    
    /**
     * @return $notificationReport
     */
    public function createReport();

    /**
     * @param NotificationReport $report
     */
    public function run(NotificationReport $report);
    
    /**
     * @param callable
     */
    public function start(callable $setReport);
    
    /**
     * @param array
     * 
     * @return array
     */
    public function valid(array $target);
    
    /**
     * @param array
     * 
     * @return array
     */
    public function parse(array $target);
}