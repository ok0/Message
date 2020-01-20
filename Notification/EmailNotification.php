<?php
namespace Message\Notification;

/**
 * 외부 메시지 전송 패키지 - Notification
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class EmailNotification extends AbstractNotification
{
    /**
     * @constant MAIL_SERVER
     */
    const MAIL_SERVER = "https://--mail-server--";
    
    /**
     * @param callable $setReport
     */
    public function start(callable $setReport) {
        $target = $this->getConfig();
        
        // input & check
        $sendDate = date("Y-m-d H:i:s");
        $valid = $this->valid($target);
        $parsed = $this->parse($target);
        
        $pkey = (empty($parsed["pkey"])) ? NULL : $parsed["pkey"];
        $to = (empty($parsed["to"])) ? NULL : $parsed["to"];
        if (!empty($valid)) {
            $isError = true;
            $code = $valid["code"];
            $message = $valid["message"];
        } else {
            // filter & post
            $result = $this->post($parsed);
            
            $code = $result["code"];
            $message = $result["message"];
            $isError = (empty($code)) ? false : true;
        }
        
        $result = ["pkey" => $pkey, "to" => $to, "isError" => $isError, "code" => $code, "message" => $message, "sendDate" => $sendDate];
        $setReport($isError, $result);
    }
    
    /**
    * @param array
    * 
    * @return array
    */
    public function valid(array $target = []) {
        $valid = [];
        
        if (empty($target["to"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(to)."];
        } else if (empty($target["from"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(from)."];
        } else if (empty($target["name"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(name)."];
        } else if (empty($target["subject"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(subject)."];
        } else if (empty($target["message"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(message)."];
        }
        
        return $valid;
    }
    
    /**
     * @param array
     * 
     * @return array
     */
    public function parse(array $target = []) {
        return $target;
    }
    
     /**
     * @param array
     * 
     * @return array
     */
    private function post(array $target = []) {
        $result = [];
        
        foreach ($target as $key => &$val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $postParams[] = $key.'='.urlencode($val);
        }
        $data = implode('&', $postParams);
        
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_URL, self::MAIL_SERVER);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_VERBOSE,true);
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		if ($response == "SUCCESS") {
            $result = [
                "code" => ""
                , "message" => ""
            ];
		} else {
		    $result = [
                "code" => "400"
                , "message" => "The connection to mail server failed."
            ];
		}
		
		return $result;
    }
}