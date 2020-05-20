<?php

namespace Siam\Sms;

class AliSms extends Base
{
    /**
     * 主机地址
     *
     * @var string
     */
    protected $host = 'dysmsapi.aliyuncs.com';

	/**
	 * 产品
	 *
	 * @var string
	 */
	private $product = 'Dysmsapi';

	/**
	 * 区域
	 *
	 * @var string
	 */
	private $region = 'cn-hangzhou';

	/**
	 * 版本
	 *
	 * @var string
	 */
	private $version = '2017-05-25';

	/**
	 * 方法
	 *
	 * @var string
	 */
	private $method = 'POST';

	/**
	 * 格式
	 *
     * @var string
     */
    public $format = 'JSON';

    /**
	 * 签名方法
	 *
	 * @var string
     */
    private $signMethod = 'HMAC-SHA1';

    /**
	 * 签名版本
	 *
	 * @var string
     */
    private $signVersion = '1.0';

    /**
     * 日期时间格式
     *
     * @var string
     */
    private $dateTimeFormat = 'Y-m-d\TH:i:s\Z';

    /**
	 * 获得数据
	 *
	 * @return array
     */
    public function getData()
    {
    	$data = [
    		'RegionId' => $this->region,
    		'TemplateCode' => $this->template,
    		'Format' => $this->format,
    		'SignatureMethod' => $this->signMethod,
    		'SignatureVersion' => $this->signVersion,
    		'SignatureNonce' => $this->uuid($this->product . $this->region),
    		'Timestamp' => gmdate($this->dateTimeFormat),
    		'Action' => 'SendSms',
    		'AccessKeyId' => $this->key,
    		'Version' => $this->version
    	];
    	$data['PhoneNumbers'] = implode(',', $this->mobiles);
    	$data['SignName'] = $this->sign;
    	$data['TemplateParam'] = json_encode($this->params);
    	return $data;
    }

    /**
	 * 生成签名
	 *
     * @param  array  $data
     * @return string
     */
    public function sign($data)
    {
    	ksort($data);
    	$string = '';
        foreach ($data as $key => $value) {
            $string .= '&' . $this->encode($key) . '=' . $this->encode($value);
        }
        $string = $this->method . '&%2F&' . $this->encode(substr($string, 1));
        return base64_encode(hash_hmac('sha1', $string, $this->secret . '&', true));
    }

    /**
     * 处理响应
     *
     * @param  array  $response
     * @return array
     */
    protected function handleResponse($response)
    {
        return ['status' => $response['Code'] == 'OK', 'code' => $response['Code'], 'message' => $response['Message']];
    }

    /**
     * 获得主机地址
     *
     * @return array
     */
    protected function getHost()
    {
        return $this->host;
    }

    /**
	 * 生成uuid
	 *
     * @param  string  $salt
     * @return string
     */
    private function uuid($salt)
    {
        return md5($salt . uniqid(md5(microtime(true)), true)) . microtime();
    }

    /**
     * 转码
     *
     * @param  string  $string
     * @return string
     */
    private function encode($string)
    {
        $string = urlencode($string);
        $string = str_replace(['+', '*'], ['%20', '%2A'], $string);
        $string = preg_replace('/%7E/', '~', $string);
        return $string;
    }
}
