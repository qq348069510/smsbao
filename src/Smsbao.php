<?php
// +----------------------------------------------------------------------
// | 短信宝快捷工具 [ Smsbao ] v1.0.0
// +----------------------------------------------------------------------
// | 适用于短信宝API v1 短信宝：http://www.smsbao.com/
// +----------------------------------------------------------------------
// | 版权所有 2019 IT老酸奶 https://github.com/qq348069510/smsbao
// +----------------------------------------------------------------------
// | 开源协议 Apache2.0 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace smsbao;

class Smsbao
{

    private $smsBaoUsername;
    private $smsBaoPassword;
    private $errorMessage;
    private $isSSL = true;

    //国内短信
    const GN_SMS = 1;
    //国外短信
    const GW_SMS = 2;
    //语音验证码
    const VOICE = 3;
    //查询
    const QUERY = 4;

    /**
     * Smsbao constructor.
     * @param string $smsBaoUsername 短信宝平台用户名
     * @param string $smsBaoPassword 短信宝平台密码
     */
    public function __construct($smsBaoUsername = "", $smsBaoPassword = "")
    {
        $this->smsBaoUsername = $smsBaoUsername;
        $this->smsBaoPassword = md5($smsBaoPassword);
    }


    /**
     * 发送短信
     * @param string $mobile 手机号码
     * @param string $content 短信内容
     * @param int $type 可选值 Smsbao::GN_SMS|Smsbao::GW_SMS|Smsbao::VOICE 默认为GN_SMS 国内短信
     * @return bool 是否发送成功
     */
    public function send($mobile, $content, $type = self::GN_SMS)
    {
        $url = $this->apiUrl($type) . "?u=" . $this->smsBaoUsername . "&p=" . $this->smsBaoPassword . "&m=" . $mobile . "&c=" . urlencode($content);
        $httpCurlGet = $this->httpCurlGet($url);
        if ($httpCurlGet == "0") {
            return true;
        } else {
            $this->errorMessage = $this->errorNoToMessage($httpCurlGet);
            return false;
        }
    }

    /**
     * 查询短信平台账户余额
     * @return array|mixed
     */
    public function query()
    {
        $url = $this->apiUrl(self::QUERY) . "?u=" . $this->smsBaoUsername . "&p=" . $this->smsBaoPassword;
        $httpCurlGet = $this->httpCurlGet($url);
        $response = explode("\n", $httpCurlGet);
        if ($response[0] == "0") {
            $info = explode(",", $response[1]);
            return "发送条数：" . $info[0] . "条，剩余条数：" . $info[1] . "条";
        } else {
            $this->errorMessage = $this->errorNoToMessage($response[0]);
            return $this->errorMessage;
        }
    }

    /**
     * 获取最近一次发生错误的错误消息
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 生成随机字符串
     * @param int $length 长度
     * @param int $numeric 是否生成纯数字 默认为否
     * @return string 随机字符串
     */
    public function random($length, $numeric = 0)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /**
     * 设置用户名密码(用于重新设置)
     * @param $smsBaoUsername
     * @param $smsBaoPassword
     */
    public function setUser($smsBaoUsername, $smsBaoPassword)
    {
        $this->smsBaoUsername = $smsBaoUsername;
        $this->smsBaoPassword = md5($smsBaoPassword);
    }

    /**
     * 设置是否使用SSL协议安全接口
     * @param bool $isSSL true使用SSL协议
     */
    public function setIsSSL($isSSL = true)
    {
        $this->isSSL = $isSSL;
    }

    /**
     * 设置接口
     * @param $type
     * @return string
     */
    private function apiUrl($type)
    {
        $protocol = $this->isSSL ? "https://" : "http://";
        switch ($type) {
            case self::GN_SMS :
                return $protocol . "api.smsbao.com/sms";
            case self::GW_SMS :
                return $protocol . "api.smsbao.com/wsms";
            case self::VOICE :
                return $protocol . "api.smsbao.com/voice";
            case self::QUERY :
                return $protocol . "api.smsbao.com/query";
            default :
                die("接口设置错误，请检查初始化参数");
        }
    }

    /**
     * 将消息编号转换成消息
     * @param $errorNo
     * @return mixed|string
     */
    private function errorNoToMessage($errorNo)
    {
        if (isset($this->errorNo()[$errorNo])) {
            return $this->errorNo()[$errorNo];
        } else {
            return "未知错误！";
        }
    }

    /**
     * 错误编号对应的错误信息列表
     * @return array
     */
    private function errorNo()
    {
        return [
            "-1" => "接口参数有误！",
            "30" => "短信平台的账号或密码错误！",
            "40" => "短信平台的账号不存在！",
            "41" => "短信平台的短信余额不足！",
            "43" => "服务器IP地址已被限制！",
            "50" => "短信内容含有敏感词！",
            "51" => "手机号码不正确！"
        ];
    }

    /**
     * Http Get请求
     * @param $url
     * @return bool|string
     */
    private function httpCurlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        //关闭URL请求
        curl_close($ch);
        //显示获得的数据
        return $response;
    }
}