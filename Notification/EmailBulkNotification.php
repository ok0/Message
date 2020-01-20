<?php
namespace Message\Notification;

/**
 * 외부 메시지 전송 패키지 - Notification
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class EmailBulkNotification extends AbstractNotification
{
    /**
     * @constant MAIL_SERVER
     */
    const MAIL_SERVER = "htts://--mail-server--";
    
    /**
     * @param callable $setReport
     */
    public function start(callable $setReport) {
        $target = $this->getConfig();
        
        // input & check
        $sendDate = date("Y-m-d H:i:s");
        $valid = $this->valid($target);
        
        if (!empty($valid)) {
            $isError = true;
            $pkey = NULL;
            $code = $valid["code"];
            $message = $valid["message"];
            
            $result = ["pkey" => $pkey, "isError" => $isError, "code" => $code, "message" => $message, "sendDate" => $sendDate];
            $setReport($isError, $result);
        } else {
            
            // filter & check & post
            $parsed = $this->parse($target);
            foreach ($parsed["body"] as $bodyKey => $body) {
                $pkey = (empty($body["pkey"])) ? NULL : $body["pkey"];
                $to = (empty($body["to"])) ? NULL : $body["to"];
                $validBody = $this->validBody($bodyKey, $body);
                if (!empty($validBody)) {
                    $code = $validBody["code"];
                    $message = $validBody["message"];
                } else {
                    $code = "";
                    $message = "";
                }
                
                $isError = (empty($code)) ? false : true;
                $result = ["pkey" => $pkey, "to" => $to, "isError" => $isError, "code" => $code, "message" => $message, "sendDate" => $sendDate];
                $setReport($isError, $result);
            }
            
            $result = $this->post($parsed);
        }
    }
    
    /**
    * @param array
    * 
    * @return array
    */
    public function valid(array $target = []) {
        $valid = [];
        
        if (empty($target["template"]) && empty($target["templateString"]) ) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(template and templateString)."];
        } else if (empty($target["body"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(body)."];
        }
        
        return $valid;
    }
    
    /**
    * @param string
    * 
    * @return array
    */
    public function validBody($key, array $body = []) {
        $valid = [];
        
        if (empty($body["to"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(body[".$key."]->to)."];
        } else if (empty($body["from"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(body[".$key."]->from)."];
        } else if (empty($body["name"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(body[".$key."]->name)."];
        } else if (empty($body["subject"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(body[".$key."]->subject)."];
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
        if (empty($target)) {
            $result = [
                "code" => "400"
                , "message" => "Required parameter is missing(post->parameter)."
            ];
        } else {
            // post
            $data = json_encode($target);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_URL, self::MAIL_SERVER);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_VERBOSE,true);
            
            $response = curl_exec($ch);
            $chError = curl_error($ch);
            
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
        }
		
		return $result;
    }
}