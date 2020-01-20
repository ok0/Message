<?php
namespace Message\Report;

use Message\Notification\NotificationInterface;

/**
 * 외부 메시지 전송 패키지 - Report(Logger)
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class NotificationReport implements ReportInterface
{
    protected $notification;
    protected $error = [];
    protected $output = [];
    protected $startTime;
    protected $endTime;

    /**
     * @param NotificationInterface $notification
     */
    public function __construct(NotificationInterface $notification) {
        $this->notification = $notification;
    }

    /**
     * @param array $line
     */
    public function addError($line) {
        $this->error[] = $line;
    }

    /**
     * @param array $line
     */
    public function addOutput($line) {
        $this->output[] = $line;
    }

    /**
     * @return NotificationInterface
     */
    public function getNotification() {
        return $this->notification;
    }

    /**
     * @return array
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getOutput() {
        return $this->output;
    }


    /**
     * @param float $endTime
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }

    /**
     * @return float
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     * @param float $startTime
     */
    public function setStartTime($startTime) {
        $this->startTime = $startTime;
    }

    /**
     * @return float
     */
    public function getStartTime() {
        return $this->startTime;
    }
}
