<?php
namespace Message\Invoker;

use Message\Report\MessageReport;
use Message\Notification\NotificationInterface;
use Message\Report\ReportInterface;

/**
 * 외부 메시지 전송 패키지 - Invoker
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class Invoker implements InvokerInterface
{
    /**
     * @var InvokerSet[]
     */
    protected $sets = [];

    /**
     * @param NotificationInterface[] $notification
     *
     * @return ReportInterface
     */
    public function execute(array $notifications) {
        $report = new MessageReport();
        
        $this->prepareSets($notifications);
        $this->startProcesses($report);
		
        return $report;
    }

    /**
     * @param NotificationInterface[] $notification
     */
    protected function prepareSets(array $notifications) {
        $this->sets = array();
        foreach ($notifications as $notification) {
            $set = new InvokerSet();
            $set->setNotification($notification);
            $set->setReport($notification->createReport());
            $this->sets[] = $set;
        }
    }

    /**
     * @param MessageReport $report
     */
    protected function startProcesses(MessageReport $messageReport) {
        foreach ($this->sets as $set) {
            $notificationReport = $set->getReport();
            $messageReport->addNotificationReport($notificationReport);
            $set->run();
        }
    }
}