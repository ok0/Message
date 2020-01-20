<?php
namespace Message\Report;

use Message\Notification\NotificationInterface;

/**
 * 외부 메시지 전송 패키지 - Report(Logger)
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class MessageReport implements ReportInterface
{
    /**
     * @var NotificationReport[]
     */
    protected $taskReports = [];

    /**
     * @param NotificationReport $report
     */
    public function addNotificationReport(NotificationReport $report) {
        $this->taskReports[] = $report;
    }

    /**
     * @return NotificationReport[]
     */
    public function getReports() {
        return $this->taskReports;
    }

    /**
     * @param NotificationInterface $notification
     *
     * @return NotificationReport|null
     */
    public function getReport(NotificationInterface $notification) {
        foreach ($this->taskReports as $report) {
            if ($report->getNotification() === $notification) {
                return $report;
            }
        }

        return null;
    }
}
