<?php
namespace Message\Invoker;

use Message\Notifications\NotificationInterface;
use Message\Report\ReportInterface;
use Message\Report\NoticationReport;

/**
 * 외부 메시지 전송 패키지 - Invoker
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class InvokerSet
{
    /**
     * @var NotificationInterface
     */
    protected $notification;
    
    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @param NotificationInterface $notification
     */
    public function setNotification($notification) {
        $this->notification = $notification;
    }

    /**
     * @return NotificationInterface
     */
    public function getNotification() {
        return $this->notification;
    }
    
    /**
     * @param ReportInterface $report
     */
    public function setReport($report)
    {
        $this->report = $report;
    }

    /**
     * @return NoticationReport
     */
    public function getReport()
    {
        return $this->report;
    }
    
    /**
     * Notification 전송 실행.
     */
    public function run() {
        $report = $this->getReport();
        $this->notification->run($report);
    }
}