<?php
/**
 * Message 패키지 사용 단순화
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class MessageLoader
{
    /**
     * @var NotificationInterface[]
     */
    private $notifications;
    
    /**
     * @var array
     */
    private $smsResult = [];
    
    /**
     * @var array
     */
    private $pushResult = [];
    
    /**
     * @var array
     */
    private $emailResult = [];
    
    /**
     * 생성자
     */
    public function __construct() {
        self::__autoload();
    }
    
    /**
     * 소멸자
     */
    public function __destruct() {
    }
    
    /**
     * 
     */
    public function clear() {
        $this->setNotifications([]);
        $this->smsResult = [];
        $this->pushResult = [];
        $this->emailResult = [];
    }
    
    /*
     * 클래스 오토로드
     */
    public static function __autoload() {
        $prefixPath = __DIR__."/";
        
        $messagePath = $prefixPath."/";
        $notificationPath = $prefixPath."Notification/";
        $resolverPath = $prefixPath."Resolver/";
        $invokerPath = $prefixPath."Invoker/";
        $reportPath = $prefixPath."Report/";
        
        require_once($messagePath."Message.php");
        require_once($notificationPath."NotificationInterface.php");
        require_once($notificationPath."AbstractNotification.php");
        require_once($notificationPath."EmailNotification.php");
        require_once($notificationPath."EmailBulkNotification.php");
        require_once($notificationPath."PushNotification.php");
        require_once($notificationPath."SmsNotification.php");
        require_once($resolverPath."ResolverInterface.php");
        require_once($resolverPath."Resolver.php");
        require_once($invokerPath."InvokerInterface.php");
        require_once($invokerPath."Invoker.php");
        require_once($invokerPath."InvokerSet.php");
        require_once($reportPath."ReportInterface.php");
        require_once($reportPath."MessageReport.php");
        require_once($reportPath."NotificationReport.php");
    }
    
    /**
     * set notifications.
     * 
     * @return array NotificationInferface[]
     * 
     */
    public function setNotifications($notifications = []) {
        return $this->notifications = $notifications;
    }
    
    /**
     * get notifications.
     * 
     * @return array NotificationInferface[]
     * 
     */
    public function getNotifications() {
        return $this->notifications;
    }
    
    /**
     * add notification.
     * 
     * @param array $config
     * 
     * @return array $result
     * 
     */
    public function addSms(array $config = []) {
        $notification = new \Message\Notification\SmsNotification();
        $notification->setConfig($config);
        
        $this->notifications[] = $notification;
    }
    
    /**
     * add notification.
     * 
     * @param array $config
     * 
     * @return array $result
     * 
     */
    public function addEmail(array $config = []) {
        $notification = new \Message\Notification\EmailNotification();
        $notification->setConfig($config);
        
        $this->notifications[] = $notification;
    }
    
    /**
     * add notification.
     * 
     * @param array $config
     * 
     * @return array $result
     * 
     */
    public function addEmailBulk(array $config = []) {
        $notification = new \Message\Notification\EmailBulkNotification();
        $notification->setConfig($config);
        
        
        $this->notifications[] = $notification;
    }
    
    /**
     * add notification.
     * 
     * @param array $config
     * 
     * @return array $result
     * 
     */
    public function addPush(array $config = []) {
        $notification = new \Message\Notification\PushNotification();
        $notification->setConfig($config);
        
        $this->notifications[] = $notification;
    }
    
    /**
     * set result.
     * 
     * @param array $config
     * 
     * @return array $result
     * 
     */
    private function setResult(\Message\Report\ReportInterface $report) {
        $notification = $report->getNotification();
        $error = $report->getError();
        $output = $report->getOutput();
        $parsed = $this->parseReport($error, $output);
        
        if ($notification instanceof \Message\Notification\SmsNotification) {
            $this->smsResult = array_merge($this->smsResult, $parsed);
        } else if ($notification instanceof \Message\Notification\PushNotification) {
            $this->pushResult = array_merge($this->pushResult, $parsed);
        } else if ($notification instanceof \Message\Notification\EmailNotification) {
            $this->emailResult = array_merge($this->emailResult, $parsed);
        } else if ($notification instanceof \Message\Notification\EmailBulkNotification) {
            $this->emailResult = array_merge($this->emailResult, $parsed);
        }
    }
    
    /**
     * @return array
     */
    public function getSmsResult() {
        return $this->smsResult;
    }
    
    /**
     * @return array
     */
    public function getPushResult() {
        return $this->pushResult;
    }
    
    /**
     * @return array
     */
    public function getEmailResult() {
        return $this->emailResult;
    }
    
    /**
     * parset report.
     * 
     * @param array $error
     * @param array $output
     * 
     * @return array $result
     * 
     */
    private function parseReport(array $error = [], array $output = []) {
        $result = [];
        $result = array_merge($error, $output);
        
        return $result;
    }
    
    /**
     * send message.
     * 
     * @return array $result
     */
    public function post() {
        if (empty($this->notifications)) {
            log_message("error", __METHOD__." - Set Notifications.");
            return false;
        }

        // set message
        $message = new \Message\Message();
        
        // set resolver
        $resolver = new \Message\Resolver\Resolver();
        $resolver->addNotifications($this->notifications);
        $message->setResolver($resolver);
        
        // set invoker
        $invoker = new \Message\Invoker\Invoker();
        $message->setInvoker($invoker);
        
        // get result
        $error = [];
        $output = [];
        $reports = $message->run()->getReports();
        
        foreach ($reports as $report) {
            $this->setResult($report);
        }
    }
}