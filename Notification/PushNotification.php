<?php
namespace Message\Notification;

/**
 * 외부 메시지 전송 패키지 - Notification
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class PushNotification extends AbstractNotification
{
    /**
     * @var API_KEY
     */
    const API_KEY = "--FCM-API-KEY--";
    
    /**
     * @var CHUNK_SIZE 1회 요청당 전송 갯수.
     */
    const CHUNK_SIZE = 1000;
    
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
            self::$module = new \Edujugon\PushNotification\PushNotification("fcm");
        }
    }
    
    /**
     * @param callable $setReport
     */
    public function start(callable $setReport) {
        self::__loadModule();
        
        $target = $this->getConfig();
        $deviceTokens = array_chunk($target["deviceTokens"], 1000);
        foreach ($deviceTokens as $deviceToken) {
            // input & check;
            $sendDate = date("Y-m-d H:i:s");
            $valid = $this->valid($target);
            $parsed = $this->parse($target);
            
            if (!empty($valid)) {
                $isError = true;
                $code = $valid["code"];
                $message = $valid["message"];
                foreach ($deviceToken as $pkey) {
                    $result = ["pkey" => $pkey, "to" => $pkey, "isError" => $isError, "code" => $code, "message" => $message, "sendDate" => $sendDate];
                    $setReport($isError, $result);
                }
            } else {
                self::$module
                    ->setApiKey(self::API_KEY)
                    ->setConfig([
                        "dry_run" => false
                        , "content_available" => true
                    ])
                    ->setDevicesToken($deviceToken)
                    ->setMessage($parsed)
                    ->send();
                $feedbacks = self::$module->getFeedback()->results;
                
                foreach ($feedbacks as $feedbackIdx => $feedback) {
                    $pkey = (empty($deviceToken[$feedbackIdx])) ? NULL : $deviceToken[$feedbackIdx];
                    $to = $pkey;
                    
                    if (isset($feedback->error)) {
                        $isError = true;
                        $code = $feedback->error;
                        $message = $feedback->error;
                    } else {
                        $isError = false;
                        $code = null;
                        $message = null;
                    }
                    
                    $result = ["pkey" => $pkey, "to" => $to, "isError" => $isError, "code" => $code, "message" => $message, "sendDate" => $sendDate];
                    $setReport($isError, $result);
                }
            }
        }
    }
    
    /**
    * @param array
    * 
    * @return array
    */
    public function valid(array $target = []) {
        $valid = [];
        
        if (empty($target["type"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(type)."];
        } else if (!in_array($target["type"], ["android", "ios"])) {
            $valid = ["code" => 412, "message" => "Required parameter is incorrect(type)."];
        } else if (empty($target["deviceTokens"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(deviceTokens)."];
        } else if (empty($target["title"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(title)."];
        } else if (empty($target["body1"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(body1)."];
        } else if (empty($target["url"])) {
            $valid = ["code" => 412, "message" => "Required parameter is missing(url)."];
        } else if ($target["type"] == "android") {
            if (empty($target["collapse_key"])) {
                $valid = ["code" => 412, "message" => "Required parameter is missing(collapse_key)."];
            }
        }
        
        return $valid;
    }
    
    /**
     * @param array
     * 
     * @return array
     */
    public function parse(array $target = []) {
        $result = [];
        
        $type = $target["type"];
        if ($type == "android") {
            $result = [
                "collapse_key" => $target["collapse_key"]
                , "data" => [
                    "title" => $target["title"]
                    , "msg" => $target["body1"]
                    , "url" => $target["url"]
                ]
            ];
            
            if (!empty($target["body2"])) {
                $result["data"]["pull_flag"] = "Y";
				$result["data"]["msg2"] = $target["body2"];
            }
            
            if (!empty($target["image"])) {
                $result["data"]["img"] = $target["image"];
            }
        } else if($type == "ios") {
            $result = [
                "notification" => [
					"title" => $target["title"]
					, "sound" => "default"
					, "body" => (!empty($target["body2"])) ? $target["body2"] : $target["body1"]
				]
				, "data" => [
					"url" => $target["url"]
				]
			];
        }
        
        return $result;
    }
}