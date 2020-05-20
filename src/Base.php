<?php

namespace Siam\Sms;

abstract class Base
{
	/**
	 * 账号key
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * 账号secret
	 *
	 * @var string
	 */
	protected $secret;

	/**
	 * 手机号
	 *
	 * @var array
	 */
	protected $mobiles;

	/**
	 * 模板
	 *
	 * @var string
	 */
	protected $template;

	/**
	 * 签名
	 *
	 * @var string
	 */
	protected $sign;

	/**
	 * 参数
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * 响应
	 *
	 * @var array
	 */
	private $response;

	/**
	 * 构造函数
	 *
	 * @param  string  $key
     * @param  string  $secret
	 * @return void
	 */
	public function __construct($key, $secret)
	{
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * 发送短信
	 *
     * @return string
     */
    public function send()
    {
        $data = $this->getData();
        $data['Signature'] = $this->sign($data);
        return $this->request($data);
    }

    /**
	 * 设置手机号
	 *
	 * @param  mixed  $mobile
	 * @return self
	 */
	public function setMobile($mobile)
	{
		$this->mobiles = $this->handleMobile($mobile);
		return $this;
	}

	/**
	 * 设置模板
	 *
	 * @param  string  $template
	 * @return self
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}

	/**
	 * 设置签名
	 *
	 * @param  string  $sign
	 * @return self
	 */
	public function setSign($sign)
	{
		$this->sign = $sign;
		return $this;
	}

	/**
	 * 设置参数
	 *
	 * @param  array  $params
	 * @return self
	 */
	public function setParams($params)
	{
		$this->params = $params;
		return $this;
	}

    /**
	 * 处理手机号
	 * 未加国际区号时默认为中国手机号
	 *
	 * @param  mixed  $mobiles
     * @return array
     */
    public function handleMobile($mobiles)
    {
    	$mobiles = is_array($mobiles) ? $mobiles : [$mobiles];
    	foreach ($mobiles as $index => $value) {
    		$first = substr($value, 0, 1);
    		if (substr($value, 0, 1) != '+') {
    			$mobiles[$index] = '+86' . $value;
    		}
    	}
    	return $mobiles;
    }

	/**
	 * 发送请求
	 *
	 * @param  array  $data
	 * @return array
     */
    protected function request($data)
    {
    	$host = 'https://' . preg_replace('/https?:\/\//', '', $this->getHost());
    	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = $this->handleResponse(json_decode($response, true));
        $this->response = $response;
        return $response['status'];
    }

    /**
	 * 获得响应
	 *
	 * @return array
     */
    public function getResponse()
    {
    	return $this->response;
    }

	/**
     * 获得数据
     *
     * @return array
     */
    abstract protected function getData();

    /**
     * 生成签名
     *
     * @param  array  $data
     * @return string
     */
    abstract protected function sign($data);

    /**
     * 处理响应
     *
     * @param  array  $response
     * @return array
     */
    abstract protected function handleResponse($response);

    /**
     * 获得主机地址
     *
     * @return array
     */
    abstract protected function getHost();
}
