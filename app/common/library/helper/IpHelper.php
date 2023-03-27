<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------
namespace app\common\library\helper;

use Pay\Exceptions\Exception;

class IpHelper
{
    private static $checkedIps = [];

    public static function is_ip($str)
    {
        $ip = explode('.', $str);
        for ($i = 0; $i < count($ip); $i++) {
            if ($ip[$i] > 255) {
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
    }

    public static function  ip(){
        $ip='未知IP';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            return self::is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return self::is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
        }else{
            return self::is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
        }
    }

	/**
	 * 获取真实ip地址
	 * @param bool $int
	 * @return array|false|float|int|mixed|string
	 */
	public static function getRealIp(bool $int=false)
	{
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }elseif (getenv('HTTP_X_REAL_IP')) {
            $ip = getenv('HTTP_X_REAL_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
            $ips = explode(',', $ip);
            $ip = $ips[0];
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = '0.0.0.0';
        }
		return $int ? ip2long($ip) : $ip;
	}

    /**
     * 获取IP所属归属地
     * @param $ip
     * @param string $type
     * @return mixed|string
     */
	public static function queryIpLocalInfo($ip,$type='text')
    {
        try {
            $url = 'http://ip-api.com/json/'.$ip.'?lang=zh-CN';
            $result = json_decode(file_get_contents($url),true);
            if($result['status']!='success')
            {
                return '内部网络';
            }
            return $type!='text' ? $result : $result['country'].$result['regionName'].$result['city'];
        }catch (Exception $exception){
            return '';
        }
    }

    /**
     * 验证 IP 地址是否为内网 IP
     * @param string
     * @return bool
     */
    public static function validInternalIp($ip): bool
    {
        if (!self::is_ip($ip))
        {
            return false;
        }
        $ip_address = explode('.', $ip);
        if ($ip_address[0] == 10)
        {
            return true;
        }
        if ($ip_address[0] == 172 and $ip_address[1] > 15 and $ip_address[1] < 32)
        {
            return true;
        }
        if ($ip_address[0] == 192 and $ip_address[1] == 168)
        {
            return true;
        }
        return false;
    }

    public static function checkIp(?string $requestIp, $ips): bool
    {
        if (null === $requestIp) {
            return false;
        }

        if (!\is_array($ips)) {
            $ips = [$ips];
        }

        $method = substr_count($requestIp, ':') > 1 ? 'checkIp6' : 'checkIp4';

        foreach ($ips as $ip) {
            if (self::$method($requestIp, $ip)) {
                return true;
            }
        }

        return false;
    }

    public static function checkIp4(?string $requestIp, string $ip)
    {
        $cacheKey = $requestIp.'-'.$ip;
        if (isset(self::$checkedIps[$cacheKey])) {
            return self::$checkedIps[$cacheKey];
        }

        if (!filter_var($requestIp, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
            return self::$checkedIps[$cacheKey] = false;
        }

        if (str_contains($ip, '/')) {
            [$address, $netmask] = explode('/', $ip, 2);

            if ('0' === $netmask) {
                return self::$checkedIps[$cacheKey] = filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4);
            }

            if ($netmask < 0 || $netmask > 32) {
                return self::$checkedIps[$cacheKey] = false;
            }
        } else {
            $address = $ip;
            $netmask = 32;
        }

        if (false === ip2long($address)) {
            return self::$checkedIps[$cacheKey] = false;
        }

        return self::$checkedIps[$cacheKey] = 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, $netmask);
    }

    public static function checkIp6(?string $requestIp, string $ip)
    {
        $cacheKey = $requestIp.'-'.$ip;
        if (isset(self::$checkedIps[$cacheKey])) {
            return self::$checkedIps[$cacheKey];
        }

        if (!((\extension_loaded('sockets') && \defined('AF_INET6')) || @inet_pton('::1'))) {
            throw new \RuntimeException('Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".');
        }

        if (str_contains($ip, '/')) {
            [$address, $netmask] = explode('/', $ip, 2);

            if ('0' === $netmask) {
                return (bool) unpack('n*', @inet_pton($address));
            }

            if ($netmask < 1 || $netmask > 128) {
                return self::$checkedIps[$cacheKey] = false;
            }
        } else {
            $address = $ip;
            $netmask = 128;
        }

        $bytesAddr = unpack('n*', @inet_pton($address));
        $bytesTest = unpack('n*', @inet_pton($requestIp));

        if (!$bytesAddr || !$bytesTest) {
            return self::$checkedIps[$cacheKey] = false;
        }

        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = $netmask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xffff >> $left) & 0xffff;
            if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) {
                return self::$checkedIps[$cacheKey] = false;
            }
        }

        return self::$checkedIps[$cacheKey] = true;
    }
}