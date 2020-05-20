<?php

namespace Siam\Sms;

class TencentSms extends Base
{
	/**
	 * 主机地址
	 *
	 * @var string
	 */
	private $host = 'sms.tencentcloudapi.com';

    /**
     * 方法
     *
     * @var string
     */
    private $method = 'POST';

    /**
     * 路由
     *
     * @var string
     */
    private $route = '/';

    /**
     * 版本
     *
     * @var string
     */
    private $version = '2019-07-11';

    /**
     * 短信应用id
     *
     * @var int
     */
    private $appid;

    /**
     * 设置appid
     *
     * @return self
     */
    public function setAppid($appid)
    {
        $this->appid = $appid;
        return $this;
    }

    /**
     * 获得数据
     *
     * @return array
     */
    protected function getData()
    {
        $data = [
            'SecretId' => $this->key,
            'Action' => 'SendSms',
            'Version' => $this->version,
            'Sign' => $this->sign,
            'Nonce' => rand(10000, 99999),
            'TemplateID' => $this->template,
            'SmsSdkAppid' => $this->appid,
            'Timestamp' => time()
        ];
        foreach ($this->mobiles as $index => $mobile) {
            $data['PhoneNumberSet.' . $index] = $mobile;
        }
        $this->params = array_values($this->params);
        foreach ($this->params as $index => $value) {
            $data['TemplateParamSet.' . $index] = $value;
        }
        return $data;
    }

    /**
     * 生成签名
     *
     * @param  array   $data
     * @return string
     */
    protected function sign($data)
    {
        ksort($data);
        $string = $this->method . $this->host . $this->route . '?';
        foreach ($data as $key => $value) {
            $string .= $key . '=' . $value . '&';
        }
        $string = rtrim($string, '&');
        return base64_encode(hash_hmac('sha1', $string, $this->secret, true));
    }

    /**
     * 处理响应
     *
     * @param  array  $response
     * @return array
     */
    protected function handleResponse($response)
    {
        $result = ['status' => false];
        if (isset($response['Response']['SendStatusSet'])) {
            foreach ($response['Response']['SendStatusSet'] as $item) {
                $result['status'] = $item['Code'] == 'Ok';
                $result['code'] = $item['Code'];
                $result['message'] = $item['Message'];
                if (!$result['status']) {
                    break;
                }
            }
        } else {
            $result['code'] = $response['Response']['Error']['Code'];
            $result['message'] = $response['Response']['Error']['Message'];
        }
        return $result;
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
}
