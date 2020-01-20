<?php
namespace Message\Invoker;

use Message\Notification\NotificationInterface;
use Message\Report\ReportInterface;

/**
 * 외부 메시지 전송 패키지 - Invoker
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
interface InvokerInterface
{
    /**
     * Execute the Notifications.
     *
     * @param NotificationInterface[] $notifications
     *
     * @return ReportInterface
     */
    public function execute(array $notifications);
}