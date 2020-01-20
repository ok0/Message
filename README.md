Message
====
외부에 메시지를 전송하는데 사용되는 패키지. (SMS, Push, Email)

Code example
------------

```php
<?php
// set message
$message = new \Message\Message();

// set notification(sms)
$notification = new \Message\Notification\SmsNotification();
$notification->setConfig($config);

// set resolver 
$resolver = new \Message\Resolver\Resolver();
$resolver->addNotification($notification);
$message->setResolver($resolver);

// set invoker
$invoker = new \Message\Invoker\Invoker();
$message->setInvoker($invoker);

// run!
$report = $message->run()->getReport($notification);

// get result
$error = $report->getError();
$output = $report->getOutput();
```


Code example IN codeigniter2
------------

```php
// load
$this->load->library("Message/messageLoader", NULL, "messageLoader");

// email
$emailConfig = [
	"pkey" => __
	, "to" => __
	, "from" => __
	, "name" => __
	, "subject" => __
	, "message" => __
];
$this->messageLoader->addEmail($emailConfig);

$bulkConfig = [
	"pkey" => null
	, "template" => __
	, "body" => [
		"to" => __
		, "from" => __
		, "name" => __
		, "subject" => __
		, "message" => __
		, "entries" => []
	]
];
$this->messageLoader->addEmailBulk($bulkConfig);

// push
$pushConfig = [
    "type" => __ (ios/android)
    , "pkey" => __
    , "title" => __
    , "body1" => __
    , "body2" => __
    , "url" => __
    , "deviceTokens" => []
];
$this->messageLoader->addPush([]);

// sms
$smsConfig = [
	[
		"pkey" => __
		, "from" => __
		, "to" => __
		, "body" => __
	]
];
$this->messageLoader->addSms($smsConfig);


// post!
$this->messageLoader->post();


// get result!
$emailResult = $this->messageLoader->getEmailResult();
$smsResult = $this->messageLoader->getSmsResult();
$pushResult = $this->messageLoader->getPushResult();


// clear queue.
$this->messageLoader->clear();
```


Requirements
------------
edujugon/push-notification : https://packagist.org/packages/edujugon/push-notification
inisis/sms(./Notification/drivers/sms/inisis/): PG사 문의