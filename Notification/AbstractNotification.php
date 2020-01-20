<?php
namespace Message\Notification;

use Message\Report\NotificationReport;

/**
 * 외부 메시지 전송 패키지 - Notification
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
abstract class AbstractNotification implements NotificationInterface
{
    /**
     * @var $config
     */
    protected $config;

    /**
     * @param array $config
     */
    public function setConfig(array $config = []) {
        $this->config = $config;
    }

    /**
     * @return array $config
     */
    public function getConfig() {
        return $this->config;
    }
    
    /**
     * @return NotificationReport
     */
    public function createReport() {
        return new NotificationReport($this);
    }
    
    /**
     * @param NotificationReport $report
     */
    public function run(NotificationReport $report) {
        $this->report = $report;
        $report->setStartTime(microtime(true));
        
        $this->start(function($isError, $buffer) use ($report) {
            if ($isError === true) {
                $report->addError($buffer);
            } else {
                $report->addOutput($buffer);
            }
        });
        
        $report->setEndTime(microtime(false));
    }
}