<?php
header('Content-type:application/json; charset=utf-8');//允许所以域名访问，可以解决App无法跨域问题
header('Access-Control-Allow-Headers:*');//允许客户端发送自定义header参数
header("Access-Control-Allow-Origin:*");//域名白名单的意思
//数组转json并且结束php脚本
	function echoJson($data,$isExit=true){
		exit(json_encode($data,320));
	}
	
//因为传递cookie不方便，所以通过get方式传递一个 session id  【这里验证一下】
	if(preg_match('/^[a-zA-Z0-9_]{0,20}$/', $_GET['session_id']) === 0){
		echoJson(array('code' => 2,'msg' => 'session_id有误'));
	}
//指定一个session 的id
	session_id($_GET['session_id']);
	session_start();//开启session
//图形验证码
	if(isset($_SESSION['tuxing_yzm_x']) === true){
		if(preg_match('/^[0-9]{1,4}$/', $_GET['x']) === 0){
			echoJson(array('code' => 2,'msg' => '位置的字符类型有误'));
		}
		if($_GET['x'] <= $_SESSION['tuxing_yzm_x']+4 && $_GET['x'] >= $_SESSION['tuxing_yzm_x']-4){//左右两边都有4px的包容度【可自由调节】
			unset( $_SESSION['tuxing_yzm_x']);//验证通过之后需要删除，否则可以重复验证
		}else{
			echoJson(array('code' => 2,'msg' => '图形验证码的偏移值有误'));
		}
	}else{
		echoJson(array('code' => 2,'msg' => '请先生成图形验证码'));
	}
//好了，下面你就可以自由愉快的发送短信验证码了
	//.......
?>