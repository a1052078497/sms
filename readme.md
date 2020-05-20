## Siam sms

## 介绍
这是一个基于阿里云和腾讯云最新短信发送功能的极简包
为了更大限度兼容更低版本的php，代码实现过程中也并没有使用类型限制
**如果你只想使用这两个云平台的短信发送功能，这个包是你不错的选择**

## 使用方法

在支持composer包的框架内使用composer安装此包

```shell
composer require siam-yon/sms
```

在具体代码中的使用方法

```php
<?php

use Siam\Sms\AliSms;
use Siam\Sms\TencentSms;

class Demo
{
	function aliSend()
	{
		$key = '你的阿里云密钥标识';
		$secret = '你的阿里云密钥密码';
		$sign = '你的阿里云短信签名';
		$template = '你的阿里云短信模板';
		// 可发送多个手机号，变量为数组即可，如：[11111111111, 22222222222]
		$mobile = 11111111111;
		// 阿里云模板变量为键值对数组
		$params = ['code' => rand(1000, 9999)];
		$sms = new AliSms($key, $secret);
		// 需要注意，设置配置不分先后顺序，send后也不会清空配置
		$result = $sms->setMobile($mobile)->setTemplate($template)->setSign($sign)->setParams($params)->send();
		/**
		 * 返回值为bool，你可获得阿里云响应做出你业务内的处理
		 *
		 * status bool 此变量是此包用来判断是否发送成功
		 * code string 阿里云短信响应代码
		 * message string 阿里云短信响应信息
		 */
		if (!$result) {
			$response = $sms->getResponse();
			// 做出处理
		}
	}
	
	function tencentSend()
	{
		$key = '你的腾讯云密钥标识';
		$secret = '你的腾讯云密钥密码';
		// 腾讯云短信发送短信需要指定应用id
		$appid = '你的腾讯云短信应用id';
		$sign = '你的腾讯云短信签名';
		$template = '你的阿里云短信模板';
		// 可发送多个手机号，变量为数组即可，如：[11111111111, 22222222222]
		$mobile = 11111111111;
		// 腾讯云模板变量为索引数组，当你传入关联数组时会按顺序变为索引数组，如：['name' => '张三', 'code' => '123'] => ['张三', '123']
		$params = [rand(1000, 9999)];
		$sms = new TencentSms($key, $secret);
		// 需要注意，设置配置不分先后顺序，send后也不会清空配置
		$result = $sms->setAppid($appid)->setMobile($mobile)->setTemplate($template)->setSign($sign)->setParams($params)->send();
		/**
		 * 返回值为bool，你可获得腾讯云响应做出你业务内的处理
		 *
		 * status bool 此变量是此包用来判断是否发送成功
		 * code string 腾讯云短信响应代码
		 * message string 腾讯云短信响应信息
		 */
		if (!$result) {
			$response = $sms->getResponse();
			// 做出处理
		}
	}
}
```

### 在Laravel中的快捷使用

安装后发布配置文件，此命令会在你的config目录下生成一个siam-sms.php文件

```shell
php artisan vendor:publish --provider="Siam\Sms\Laravel\ServiceProvider"
```

在env文件中配置阿里云或腾讯云短信密钥，当然你也可以修改env使用的键名 或者 直接明文写在siam-sms.php配置文件中

```env
# 阿里云短信服务密钥
ALI_SMS_KEY=
ALI_SMS_SECRET=

# 腾讯云短信服务密钥
TENCENT_SMS_KEY=
TENCENT_SMS_SECRET=
```

在使用前，你需要在‘actions’内配置好一些**动作模板**，如：注册、登录等，当你认为不同的动作之间的短信应该有**模板、签名**等有差异时，那么你就应该在actions内配置好对应的配置
**需要注意使用TencentSms作为驱动时，每个动作内应该要有应用id**

```php
<?php

use Siam\Sms\AliSms;
use Siam\Sms\TencentSms;

return [
	'ali' => [
		'key' => env('ALI_SMS_KEY'),
		'secret' => env('ALI_SMS_SECRET'),
		'drive' => AliSms::class,
		'actions' => [
			'register' => [
				'sign' => 'xxx论坛',
				'template' => '阿里云模板id',
			],
			'payment' => [
				'sign' => 'xxx商城',
				'template' => '阿里云模板id'
			]
		]
	],
	'tencent' => [
		'key' => env('TENCENT_SMS_KEY'),
		'secret' => env('TENCENT_SMS_SECRET'),
		'drive' => TencentSms::class,
		'actions' => [
			'register' => [
				'sign' => 'xxx论坛',
				'template' => '腾讯云模板id',
				'appid' => '腾讯云应用id'
			]
		]
	]
];
```

使用实例化时传入你使用的键名，默认的配置文件键名有 'ali' 和 'tencent' 当然你可以任意的按照自己想法进行更改键名或添加键名

```php
use SiamSms;

$sms = new SiamSms('tencent');
// 定义动作
$action = 'register';
// 可发送多个手机号，变量为数组即可，如：[11111111111, 22222222222]
$mobile = 11111111111;
// 使用腾讯云时，当你传入关联数组时会按顺序变为索引数组，如：['name' => '张三', 'code' => '123'] => ['张三', '123']
$params = ['code' => rand(1000, 9999)];
$result = $sms->send($action, $mobile, $params);
/**
 * 返回值为bool，你可获得腾讯云响应做出你业务内的处理
 *
 * status bool 此变量是此包用来判断是否发送成功
 * code string 腾讯云短信响应代码
 * message string 腾讯云短信响应信息
 */
if (!$result) {
	$response = $sms->getResponse();
	// 做出处理
}
```

你也可以获得实际使用的驱动

```php
$sms->getDirve();
```

### 当你有使用问题时可以通过以下方式联系，我会在有空闲时间后第一时间回复
* 发送邮件到**1052078497@qq.com**
* 加入qq群**177987594**提问