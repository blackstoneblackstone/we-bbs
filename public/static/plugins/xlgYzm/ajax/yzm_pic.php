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
	session_id($_GET['session_id']);//指定一个session 的id
	session_start();//开启session
if($_GET['type'] === 'shengcheng'){//生成
	//【随机】裁剪的区域坐标【左上角】【并保存】
		$_SESSION['tuxing_yzm_x'] = rand(130,550);
		$_SESSION['tuxing_yzm_y'] = rand(50, 260);
		$_SESSION['tuxing_yzm_img'] = '../style/i/yzm_pic/'.rand(1, 61).'.jpg';//原图要求是 1920x1080这个比例的，679x382【图片避免用白色背景的】
		$_SESSION['tuxing_yzm_moban'] = '../style/i/yzm_pic/moban/'.rand(1, 4).'.png';
		$_SESSION['tuxing_yzm_opacity'] = rand(30, 80);//原图上空缺的位置的透明度【这个可以增加被破解的记录】
		$_SESSION['tuxing_yzm_time'] = time() + 600;//设置失效时间 600=10分钟
		$_SESSION['tuxing_yzm_error_cishu'] = 2;//验证图形验证码时，错误次数不能超过2次
		
	echoJson(array('code' => 1,'msg' => '图形滑动验证码已生成'));
}elseif($_GET['type'] === 'yanzheng'){//验证
	if(!(
		isset($_SESSION['tuxing_yzm_x']) && 
		isset($_SESSION['tuxing_yzm_error_cishu']) && 
		isset($_SESSION['tuxing_yzm_time'])
	)){
		echoJson(array('code' => 2,'msg' => '请先获取图形验证码'));
	}
	
	if($_SESSION['tuxing_yzm_time'] <= time()){
		echoJson(array('code' => 2,'msg' => '验证码已过期，有效期为10分钟'));
	}
	if(preg_match('/^[0-9]{1,4}$/', $_GET['x']) === 0){
		echoJson(array('code' => 2,'msg' => '位置的字符类型有误'));
	}
	if($_GET['x'] <= $_SESSION['tuxing_yzm_x']+4 && $_GET['x'] >= $_SESSION['tuxing_yzm_x']-4){//左右两边都有4px的包容度
		echoJson(array('code' => 1,'msg' => '验证成功'));
	}else{
		$_SESSION['tuxing_yzm_error_cishu'] -= 1;
		if($_SESSION['tuxing_yzm_error_cishu'] == 0){
			echoJson(array('code' => 2,'msg' => '验证码错误次数过多，请重新获取'));
		}
		echoJson(array('code' => 2,'msg' => '图形滑块验证码位置有误'));
	}
}else{//加载主图 跟 缺口图
	//获取 SESSION 信息
		if(!(
			isset($_SESSION['tuxing_yzm_x']) && 
			isset($_SESSION['tuxing_yzm_y']) && 
			isset($_SESSION['tuxing_yzm_img']) && 
			isset($_SESSION['tuxing_yzm_moban']) && 
			isset($_SESSION['tuxing_yzm_opacity'])
		)){
			echoJson(array('code' => 2,'msg' => '图形滑动验证码尚未生成'));
		}
		$x = $_SESSION['tuxing_yzm_x'];//设置验证码
		$y = $_SESSION['tuxing_yzm_y'];//设置验证码
		$img = $_SESSION['tuxing_yzm_img'];
		$moban = $_SESSION['tuxing_yzm_moban'];
		$opacity = $_SESSION['tuxing_yzm_opacity'];
	//创建源图的实例
		$src = imagecreatefromstring(file_get_contents($img));
	//新建一个真彩色图像【尺寸 = 90x90】【目前是不透明的】
		$res_image = imagecreatetruecolor(90, 90);
		//创建透明背景色，主要127参数，其他可以0-255，因为任何颜色的透明都是透明
			$transparent = imagecolorallocatealpha($res_image, 255, 255, 255, 127);
		//指定颜色为透明（做了移除测试，发现没问题）
			imagecolortransparent($res_image, $transparent);
		//填充图片颜色【填充会将相同颜色值的进行替换】
			imagefill($res_image, 0, 0, $transparent);//左边的半圆
	
	//实现两个内凹槽【填补上纯黑色】
	    $tempImg = imagecreatefrompng($moban);//加载模板图
		for($i=0; $i < 90; $i++){// 遍历图片的像素点
			for ($j=0; $j < 90; $j++) {
				if(imagecolorat($tempImg, $i, $j) !== 0){// 获取模板上某个点的色值【取得某像素的颜色索引值】【0 = 黑色】
					$rgb = imagecolorat($src, $x + $i, $y + $j);// 对应原图上的点
					imagesetpixel($res_image, $i, $j, $rgb);// 移动到新的图像资源上
				}
			}
		}
	if($_GET['type'] === 'zhutu'){
		//制作一个半透明白色蒙版
			$mengban = imagecreatetruecolor(90, 90);
			//先让蒙版变成透明的
				//指定颜色为透明（做了移除测试，发现没问题）
					imagecolortransparent($mengban, $transparent);
				//填充图片颜色【填充会将相同颜色值的进行替换】
					imagefill($mengban, 0, 0, $transparent);
			$huise = imagecolorallocatealpha($res_image, 255, 255, 255, $opacity);
			for($i=0; $i < 90; $i++){// 遍历图片的像素点
				for ($j=0; $j < 90; $j++) {
					$rgb = imagecolorat($res_image, $i, $j); // 获取模板上某个点的色值【取得某像素的颜色索引值】
					if($rgb !== 2147483647){// 获取模板上某个点的色值【取得某像素的颜色索引值】【0 = 黑色】
						imagesetpixel($mengban, $i, $j, $huise);// 对应点上画上黑色
					}
				}
			}
		//把修改后的图片，放回原本的位置
			imagecopyresampled(
				$src,//裁剪后的存放图片资源
				$res_image,//裁剪的原图资源
				$x, $y,//存放的图片，开始存放的位置
				0,0,//开始裁剪原图的位置
				90, 90,//存放的原图宽高
				90, 90//裁剪的原图宽高
			);
		//把蒙版添加到原图上去
			imagecopyresampled(
				$src,//裁剪后的存放图片资源
				$mengban,//裁剪的原图资源
				$x+1, $y+1,//存放的图片，开始存放的位置
				0,0,//开始裁剪原图的位置
				90-2, 90-2,//存放的原图宽高
				90, 90//裁剪的原图宽高
			);
		header('Content-Type: image/jpeg');
		imagejpeg($src);//浏览器 输出图片
	}elseif($_GET['type'] === 'futu'){
		//补上白色边框
			$tempImg = imagecreatefrompng($moban.'.png');//加载模板图
			$white = imagecolorallocatealpha($res_image, 255, 255, 255, 1);
			for($i=0; $i < 90; $i++){// 遍历图片的像素点
				for ($j=0; $j < 90; $j++) {
					if(imagecolorat($tempImg, $i, $j) === 0){// 获取模板上某个点的色值【取得某像素的颜色索引值】【0 = 黑色】
						imagesetpixel($res_image, $i, $j, $white);// 对应点上画上黑色
					}
				}
			}
		//创建一个90x382宽高 且 透明的图片
			$res_image2 = imagecreatetruecolor(90, 382);
			//指定颜色为透明（做了移除测试，发现没问题）
				imagecolortransparent($res_image2, $transparent);
			//填充图片颜色【填充会将相同颜色值的进行替换】
				imagefill($res_image2, 0, 0, $transparent);//左边的半圆
		//把裁剪的图片，移到新图片上
			imagecopyresampled(
				$res_image2,//裁剪后的存放图片资源
				$res_image,//裁剪的原图资源
				0, $y,//存放的图片，开始存放的位置
				0, 0,//开始裁剪原图的位置
				90, 90,//存放的原图宽高
				90, 90//裁剪的原图宽高
			);
		header('Content-Type: image/png');
		imagepng($res_image2);//浏览器 输出图片
	}
}
?>
