<?php
namespace Message\Resolver;

use Message\Notification\NotificationInterface;

/**
 * 외부 메시지 전송 패키지 - Resolver
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class Resolver implements ResolverInterface
{
    /**
     * @var NotificationInterface[]
     */
    protected $notifications;

    /**
     * @param array $notifications
     */
    public function __construct(array $notifications = []) {
        $this->notifications = $notifications;
    }

    /**
     * @param NotificationInterface $notification
     */
    public function addNotification(NotificationInterface $notification) {
        $this->notifications[] = $notification;
    }

    /**
     * @param NotificationInterface[] $notifications
     */
    public function addNotifications(array $notifications) {
        $this->notifications = array_merge($this->notifications, $notifications);
    }

    /**
     * @return NotificationInterface[]
     * 
     * to-do : 
     */
    public function resolve() {
        /*
        $notifications = [];
		
        foreach ($this->notifications as $notification) {
            if ($notification->is_valid($notification) === true) {
                $notifications[] = $notification;
            }
        }
        */
        
        return $this->notifications;
    }
}