<?php

use Siam\Sms\AliSms;
use Siam\Sms\TencentSms;

return [
	'ali' => [
		'key' => env('ALI_SMS_KEY'),
		'secret' => env('ALI_SMS_SECRET'),
		'drive' => AliSms::class,
		'actions' => []
	],
	'tencent' => [
		'key' => env('TENCENT_SMS_KEY'),
		'secret' => env('TENCENT_SMS_SECRET'),
		'drive' => TencentSms::class,
		'actions' => []
	]
];
