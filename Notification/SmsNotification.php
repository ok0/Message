<?php
namespace Message\Notification;

/**
 * 외부 메시지 전송 패키지 - Notification
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class SmsNotification extends AbstractNotification
{
    /**
     * @var $path
     */
    protected $path = __DIR__."/drivers/sms/inisis/";
    
    /**
     * @var $mid
     */
    protected $mid = "--INISIS-SMS-MID--";
    
    /**
     * @var $smsKey
     */
    protected $smsKey = "--INISIS-SMS-KEY--";
    
    /**
     * @var $smsType
     */
    protected $smsType = "SMS";
    
    /**
     * @var $senderName
     */
    protected $senderName = "SMS";
    
    /**
     * @var $debugType
     */
    protected $debugType = false;
    
    /**
     * @var object
     */
    private static $module;
    
    /**
     * Load the SMS drivers.
     *
     * @param 
     */
    private function __loadModule() {
        if (empty(self::$module)) {
            require_once($this->path."INISMSLib.php");
            
            self::$module = new \INIsms50();
            self::$module->SetField("inipayhome", $this->path);
			self::$module->SetField("MID", $this->mid);
			self::$module->SetField("smskey", $this->smsKey);
			self::$module->SetField("type", $this->smsType);
			self::$module->SetField("debug", $this->debugType);
			self::$module->SetField("send_emp", $this->senderName);
			self::$module->SetField("log", "false");
			self::$module->SetField("lms_yn", "N");
        }
    }
    
    /**
     * @param callable $setReport
     */
    public function start(callable $setReport) {
        self::__loadModule();
        
        $target = $this->getConfig();
        foreach ($target as $row) {
            // input & check
            $sendDate = date("Y-m-d H:i:s");
            $valid = $this->valid($row);
            $parsed = $this->parse($row);
            
            $pkey = (empty($parsed["pkey"])) ? NULL : $parsed["pkey"];
            $to = (empty($parsed["to"])) ? NULL : $parsed["to"];
            if (!empty($valid)) {
                $isError = true;
                $code = $valid["code"];
                $message = $valid["message"];
            } else {
                // filter
                self::$module->SetField("send_hp", $parsed["from"]);
                self::$module->SetField("recv_hp", $parsed["to"]);
                self::$module->SetField("send_bd", $parsed["body"]);
                self::$module->startAction();
                
                $code = self::$module->GetResult("ResultCode");
                $message = self::$module->GetResult("ResultMsg");
                $message = iconv("euc-kr", "utf-8", $message);
                
                $isError = ($code != "0000") ? true : false;
            }
            
            $result = ["pkey" => $pkey, "to" => $to, "isError" => $isError, "code" => $code, "message" => $message, "sendDate" => $sendDate];
            $setReport($isError, $result);
        }
    }
    
    /**
     * @param array
     * 
     * @return array
     */
    public function valid(array $target = []) {
        $valid = [];
        
        $pkey = (empty($target["to"])) ? NULL : $target["pkey"];
        $to = (empty($target["to"])) ? NULL : $target["to"];
        $from = (empty($target["from"])) ? NULL : $target["from"];
        $body = (empty($target["body"])) ? NULL : $target["body"];
        
        if (empty($target)) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(config)."];
        } else if (empty($pkey)) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(pkey)."];
        } else if (empty($to)) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(to)."];
        } else if (empty($from)) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(from)."];
        } else if (empty($body)) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(body)."];
        }
        
        return $valid;
    }
    
    /**
     * @param array
     * 
     * @return array
     */
    public function parse(array $target = []) {
        $body = (empty($target["body"])) ? "" : $target["body"];
        $body = stripslashes($body);
        $body = iconv("utf-8", "euc-kr", $body);
        $body = mb_strcut($body, 0, 80, "euc-kr");
        $target["body"] = $body;
        
        return $target;
    }
}