<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/
// 导入 SMS 的 client
use TencentCloud\Sms\V20190711\SmsClient;
// 导入要请求接口对应的 Request 类
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Credential;
// 导入可选配置类
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

if (!defined('IN_ANWSION'))
{
    die;
}

class main extends AWS_CONTROLLER
{
    public function get_access_rule()
    {
        $rule_action['rule_type'] = "black";
        return $rule_action;
    }

    public function sendSms_action($data=[])
    {
        $data['mobile']=trim($_POST['mobile']);
        if(!preg_match("/^1[345789]\d{9}$/",$data['mobile']))
        {
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('手机格式不正确')));
        }

        if (!$this->model('tools')->geetest($_POST) && !isset($_POST['type']))
        {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t("验证码错误")));
        }

        $setting=get_setting('sms_config');
        if($setting['dy']['status']!='Y' && $setting['sy']['status']!='Y' && $setting['tx']['status']!='Y' && $setting['hx']['status']!='Y')
        {
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信功能尚未开启')));
        }
        if($setting['dy']['status']=='Y')
        {
            if(empty($setting['dy']['accessKeyId']) || empty($setting['dy']['accessKeySecret']) || empty($setting['dy']['SignName']) || empty($setting['dy']['TemplateCode']))
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
            else
            $this->send_dy_sms($data,$setting['dy']);
        }elseif($setting['sy']['status']=='Y')
        {
            if(empty($setting['sy']['account']) || empty($setting['sy']['pswd']) )
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
            else
            $this->send_sy_sms($data,$setting['sy']);
        }elseif($setting['tx']['status']=='Y')
        {
            if(empty($setting['tx']['accessKeyId']) || empty($setting['tx']['accessKeySecret'])  || empty($setting['tx']['TemplateCode']))
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));       
            $code = "";
            for ($i = 0; $i < 6; $i++) {
                $code .= rand(0, 9);
            }
            $params = array($code,2);   //  2 表示验证码有效时间
            $data['TemplateParam']['code'] = $code;
            $this->send_tx_sms($data,$setting['tx'],$params);
        }elseif($setting['hx']['status']=='Y')
        {
            if(empty($setting['hx']['appName']) || empty($setting['hx']['org_name'])  || empty($setting['hx']['api']))
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
            $this->send_hx_sms($data,$setting['hx']);
        }
    }

    public function sendSmsApi_action($data=[])
    {
        $data['mobile']=trim($_POST['mobile']);
        if(!preg_match("/^1[345789]\d{9}$/",$data['mobile'])){
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('手机格式不正确')));
        }

        $setting=get_setting('sms_config');
        if($setting['dy']['status']!='Y' && $setting['sy']['status']!='Y' && $setting['tx']['status']!='Y' && $setting['hx']['status']!='Y'){
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信功能尚未开启')));
        }

        if($_POST['type'] == 'register')//注册
        {
            //没注册
            if ($user_info = $this->model('account')->get_user_info_by_mobile($data['mobile']))
            {   
                H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('手机号已注册')));
            }

        }else if($_POST['type'] == 'bind')//绑定
        {
            //没注册
            if ($user_info = $this->model('account')->get_user_info_by_mobile($data['mobile']))
            {   
                H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('手机号已绑定')));
            }
            //没注册

        }else if($_POST['type'] == 'find')//找回
        {
            //没注册
            if (!$user_info = $this->model('account')->get_user_info_by_mobile($data['mobile']))
            {   
                H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('手机号未绑定')));
            }
            //必须注册
        }

        if($setting['dy']['status']=='Y'){
            if(empty($setting['dy']['accessKeyId']) || empty($setting['dy']['accessKeySecret']) || empty($setting['dy']['SignName']) || empty($setting['dy']['TemplateCode']))
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
            else
            $this->send_dy_sms($data,$setting['dy']);
        }elseif($setting['sy']['status']=='Y'){
            if(empty($setting['sy']['account']) || empty($setting['sy']['pswd']) )
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
            else
            $this->send_sy_sms($data,$setting['sy']);
        }elseif($setting['tx']['status']=='Y'){
            if(empty($setting['tx']['accessKeyId']) || empty($setting['tx']['accessKeySecret']) || empty($setting['tx']['TemplateCode']))
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
            $code = "";
            for ($i = 0; $i < 6; $i++) {
                $code .= rand(0, 9);
            }
            $data['TemplateParam']['code'] = $code;
            $this->send_tx_sms($data,$setting['tx']);
        }elseif($setting['hx']['status']=='Y')
        {
            if(empty($setting['hx']['appName']) || empty($setting['hx']['org_name'])  || empty($setting['hx']['api']))
                H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信尚未配置')));
            $this->send_hx_sms($data,$setting['hx']);
        }
    }

    public function send_dy_sms($data,$account){
        $data['TemplateParam']=array('code'=>rand(1000,9999));
        $params = array();
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = $account['accessKeyId'];
        $accessKeySecret =  $account['accessKeySecret'];
        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $data['mobile'];
        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $account['SignName'];
        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $account['TemplateCode'];
        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $data['TemplateParam'];
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        $content = $this->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );
        if (!isset($content) || $content->Code != 'OK') {
            H::ajax_json_output(AWS_APP::RSM(null, -1, "短信发送失败"));
        }else{
            $session_data = array(
                'mobile' => $data['mobile'],
                'code' =>$data['TemplateParam']['code'],
                'type' => 1,
                'next_send_time' => time() + 300,
                'expire' => time() + 300
            );
            AWS_APP::session()->send_info[$data['mobile']] = $session_data;
            AWS_APP::cache()->set($data['mobile'],$session_data ,300);
            //插入短信记录
            $this->model('tools')->save_note($data['mobile'], '阿里大鱼', $account['TemplateCode'], $data['TemplateParam']['code']);
            H::ajax_json_output(AWS_APP::RSM(null, 1, "短信发送成功"));
        }
    }

    public function send_sy_sms($data,$account){
        $post_data = array();
        $post_data['account'] = $account['account'];   //示远帐号
        $post_data['pswd'] =$account['pswd'];   //示远密码

        $data['TemplateParam'] = array('code'=>rand(1000,9999));
        $msg = '您的验证码是：'.$data['TemplateParam']['code'];

        $post_data['msg'] =urlencode($msg); //短信内容，需要用urlencode编码下，注意内容中的逗号请使用中文状态下的逗号
        $post_data['mobile'] = $data['mobile']; //手机号码，多个号码使用","分割
        $post_data['product'] = ''; //产品ID(不用填写)
        $post_data['needstatus']='false'; //是否需要状态报告，需要true，不需要false
        $post_data['extno']='';  //扩展码(不用填写)
        $post_data['resptype']='json'; 
        $url='http://send.18sms.com/msg/HttpBatchSendSM';
        $o='';
        foreach ($post_data as $k=>$v)
        {
           $o.="$k=".urlencode($v).'&';
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 如果需要将结果直接返回到变量里，那加上这句
        $result = curl_exec($ch);
        $result=json_decode($result,true);
        if($result['result']>0){
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('短信发送失败')));
        }else{
            $session_data = array(
                'mobile' => $data['mobile'],
                'code' => $data['TemplateParam']['code'],
                'type' => 0,
                'next_send_time' => time() + 300,
                'expire' => time() + 300
            );
            AWS_APP::session()->send_info[$data['mobile']] = $session_data;

            AWS_APP::cache()->set($data['mobile'],$session_data,300);
            //插入短信记录
            $this->model('tools')->save_note($data['mobile'], '示远短信', '', $msg);

            H::ajax_json_output(AWS_APP::RSM(null, '1', AWS_APP::lang()->_t('短信发送成功')));

        }
    }

    public function send_tx_sms($data,$account)
    {
        if(!$data['mobile'] || !$account['TemplateCode'])
        {
            return false;
        }
	    // 短信应用SDK AppID
	    $appid = $account['accessKeyId']; // 1400开头

	    $SecretId = $account['SecretId'];
	    // 短信应用SDK AppKey
	    $appkey = $account['accessKeySecret'];
	    // 签名
	    $smsSign = $account['SignName'];
	    // 模板id
	    $templateCode = $account['TemplateCode'];

	    try {
		    $cred = new Credential($SecretId,  $appkey);
		    // 实例化一个 http 选项，可选，无特殊需求时可以跳过
		    $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");
		    // 实例化一个 client 选项，可选，无特殊需求时可以跳过
		    $clientProfile = new ClientProfile();
		    $clientProfile->setSignMethod("TC3-HMAC-SHA256");  // 指定签名算法（默认为 HmacSHA256）
		    $clientProfile->setHttpProfile($httpProfile);

		    // 实例化 SMS 的 client 对象，clientProfile 是可选的
		    $client = new SmsClient($cred, "", $clientProfile);

		    // 实例化一个 sms 发送短信请求对象，每个接口都会对应一个 request 对象。
		    $req = new SendSmsRequest();

		    /* 短信应用 ID: 在 [短信控制台] 添加应用后生成的实际 SDKAppID，例如1400006666 */
		    $req->SmsSdkAppid = $appid;
		    /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名，可登录 [短信控制台] 查看签名信息 */
		    $req->Sign = $smsSign;
		    /* 短信码号扩展号: 默认未开通，如需开通请联系 [sms helper] */
		    $req->ExtendCode = "0";
		    /* 下发手机号码，采用 e.164 标准，+[国家或地区码][手机号]
			   * 例如+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号，最多不要超过200个手机号*/
		    $req->PhoneNumberSet = ['+86'.$data['mobile']];
		    /* 国际/港澳台短信 senderid: 国内短信填空，默认未开通，如需开通请联系 [sms helper] */
		    $req->SenderId = "";
		    /* 用户的 session 内容: 可以携带用户侧 ID 等上下文信息，server 会原样返回 */
		    $req->SessionContext = "";
		    /* 模板 ID: 必须填写已审核通过的模板 ID。可登录 [短信控制台] 查看模板 ID */
		    $req->TemplateID = $templateCode;
		    /* 模板参数: 若无模板参数，则设置为空*/
		    $code = '';
		    for ($i = 0; $i < 6; $i++) {
			    $code .= rand(0, 9);
		    }
		    $req->TemplateParamSet = array($code,'2');
		    // 通过 client 对象调用 SendSms 方法发起请求。注意请求方法名与请求对象是对应的
		    $resp = $client->SendSms($req);
		    $resp = $resp->toJsonString();
		    $rsp = json_decode($resp,true);
		    if($rsp['SendStatusSet'][0]['Code'] == 'Ok')
		    {
			    $session_data = array(
				    'mobile' => $data['mobile'],
				    'code' =>$code,
				    'type' => 1,
				    'next_send_time' => time() + 300,
				    'expire' => time() + 300
			    );
			    AWS_APP::session()->send_info[$data['mobile']] = $session_data;

			    AWS_APP::cache()->set($data['mobile'],$session_data,300);
			    //插入短信记录
			    $this->model('tools')->save_note($data['mobile'], '腾讯云短信', $templateCode, $code);
			    H::ajax_json_output(AWS_APP::RSM(null, 1, "短信发送成功"));
		    }else{
			    H::ajax_json_output(AWS_APP::RSM(null, -1, "短信发送失败"));
		    }
		    return $rsp;
	    }
	    catch(TencentCloudSDKException $e) {
		    echo $e;
	    }
    }

    public function send_hx_sms($data,$account)
    {
        if(!$data['mobile'] || !$account['TemplateCode'])
        {
            return false;
        }
        $appName = $account['appName'];
        $org_name = $account['org_name'];
        $mobile = $data['mobile'];
        $client_id = $account['client_id'];
        $client_secret = $account['client_secret'];
        // 模板id
        $templateCode = $account['TemplateCode'];
        $url = $account['api'];
        try {
            $send_url = $url.'/'.$org_name.'/'.$appName.'/sms/send';
            $token_url = $url.'/'.$org_name.'/'.$appName.'/token';
            $token_res = $this->json_post($token_url,[
                'grant_type'=>'client_credentials',
                'client_id'=>$client_id,
                'client_secret'=>$client_secret
            ]);
            $token_res = json_decode($token_res,true);
            $token = $token_res['access_token'];
            $code = "";
            for ($i = 0; $i < 6; $i++) {
                $code .= rand(0, 9);
            }
            $rsp = $this->json_post($send_url,[
                'mobiles'=>[$mobile],
                'tid'=>$templateCode,
                'tmap'=>['code'=>$code],
            ],$token);
            $rsp = json_decode($rsp,true);
            if($rsp['count'])
            {
                $session_data = array(
                    'mobile' => $mobile,
                    'code' =>$code,
                    'type' => 1,
                    'next_send_time' => time() + 300,
                    'expire' => time() + 300
                );
                AWS_APP::session()->send_info[$data['mobile']] = $session_data;

                AWS_APP::cache()->set($data['mobile'],$session_data,300);
                //插入短信记录
                $this->model('tools')->save_note($data['mobile'], '环信短信', $templateCode, $code);
                H::ajax_json_output(AWS_APP::RSM(null, 1, "短信发送成功"));
            }else{
                H::ajax_json_output(AWS_APP::RSM(null, -1, "短信发送失败"));
            }
            return $rsp;
        }
        catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function request($accessKeyId, $accessKeySecret, $domain, $params, $security = false)
    {
        $apiParams = array_merge(array(
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http') . "://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";

        try {
            $content = $this->fetchContent($url);
            return json_decode($content);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $rtn = curl_exec($ch);
        if ($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }

    public function checkSmsCode_action($mobile='',$chk=''){
        $mobile=$mobile?$mobile:trim($_POST['mobile']);
        $chk=$chk?$chk:trim($_POST['code']);
        $re = $this->model('tools')->checkSmsCode($mobile,$chk);
        if($re){
            H::ajax_json_output(AWS_APP::RSM(null, 1, 'ok'));
        }
    }
 
    public function check_status_action(){}

    /*用户是否设置交易密码*/
    public function have_password_action()
    {
        $have=$this->model('account')->fetch_row('user_account','uid='.$this->user_id);
        $data['ret']=$have['deal_pwd'] ? 1: 0;
        $data['balance']=$have['balance'];
        H::ajax_json_output($data);
    }

    /*检测交易密码是否正确*/
    public function check_password_action()
    {
        $have=$this->model('account')->fetch_row('user_account','uid='.$this->user_id);
        $pass=trim($_POST['pass']);
        if(md5(md5($pass).$have['deal_salt'])==$have['deal_pwd']){
            $ret=1;
        }else{
            $ret=2;
        }
        H::ajax_json_output($ret);
    }

    public function add_account_action()
    {
        $this->model('account')->get_users_uid();
        H::redirect_msg(AWS_APP::lang()->_t('添加用户账户完成'), '/');
    }

    public function json_post($url, $data = NULL,$token=null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if(is_array($data))
        {
            $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_POST, 1);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($data),
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Authorization: Bearer '.$token
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        $errorno = curl_errno($curl);
        if ($errorno) {
            return $errorno;
        }
        curl_close($curl);
        return $res;
    }
}