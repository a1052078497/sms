<?php

namespace Siam\Sms\Laravel;

use Siam\Sms\AliSms;
use Siam\Sms\TencentSms;

class Sms
{
	/**
	 * 具体使用的对象
	 *
	 * @var \Siam\Sms\Base
	 */
	private $drive;

	/**
	 * 配置
	 *
	 * @var array
	 */
	private $config;

	/**
	 * 构造函数
	 *
	 * @param  string  $drive
	 * @return void
	 */
	public function __construct($drive)
	{
		$this->config = config('siam-sms.' . $drive);
		$this->drive = new $this->config['drive']($this->config['key'], $this->config['secret']);
	}

	/**
	 * 发送短信
	 *
	 * @param  string  $action
	 * @param  mixed   $mobile
	 * @param  array   $params
	 */
	public function send($action, $mobile, $params)
	{
		$config = $this->config['actions'][$action];
		if ($this->drive instanceof TencentSms) {
			$this->drive->setAppid($config['appid']);
		}
		return $this->drive->setMobile($mobile)->setParams($params)->setTemplate($config['template'])->setSign($config['sign'])->send();
	}

	/**
	 * 获得响应
	 *
	 * @return array
	 */
	public function getResponse()
	{
		return $this->drive->getResponse();
	}

	/**
	 * 获得驱动
	 *
	 * @return \Siam\Sms\Base
	 */
	public function getDrive()
	{
		return $this->drive;
	}
}
