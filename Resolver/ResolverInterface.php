<?php
namespace Message\Resolver;

use Message\Notification\NotificationInterface;

/**
 * 외부 메시지 전송 패키지 - Resolver
 * 
 * @author      $notification
 * @copyright   Copyright (c) 2019, $notification
 */
interface ResolverInterface
{
    /**
     * Return all available Notifications.
     *
     * @return NotificationInterface[]
     */
    public function resolve();
}