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

class ImageHelper
{
    private static $src;
    private static $image;
    private static $imageinfo;
    private static $percent = 0.7;


    /**
     * 提取HTML文章中的图片地址
     * @param string $data HTML或者文章
     * @param int $num 第 $num 个图片的src，默认为第一张
     * @param string $order 顺取倒取； 默认为 asc ，从正方向计数。 desc 从反方向计数
     * @param string|array $blacklist 图片地址黑名单，排除图片地址中包含该数据的地址；例如 传入 baidu.com  会排除 src="http://www.baidu.com/img/a.png"
     * @param string $model 默认为字符串模式;可取值 string  preg；string模式处理效率高，PHP版本越高速度越快，可比正则快几倍
     * @return array|bool|string|null  当data为空时返回 false ， src不存在时返回 null ，反之返回src
     */
    public static function src($data, $num = 1, $order = 'asc', $blacklist = false, $model = 'string'){

        if(isset($data)){
            if($model === 'preg'){
                $imgSrc = self::pregModel($data, $num-1, $order);
            }else{
                $imgSrc = self::strModel($data, $num, $order);
            }
            if($blacklist === false){
                return $imgSrc;
            }else{
                if(is_array($blacklist)){
                    foreach($blacklist as $value){
                        if(strpos($imgSrc, $value) !== false){
                            return self::src($data, $num+1, $order, $blacklist, $model);
                        };
                    }
                    return $imgSrc;
                }else{
                    if(strpos($imgSrc, (string)$blacklist) === false){
                        return $imgSrc;
                    }else{
                        return self::src($data, $num+1, $order, $blacklist, $model);
                    }
                }
            }
        }else{
            return false;
        }

    }

    /**
     * 提取HTML文章中的图片地址
     * @param string $data HTML或者文章
     * @param int $startNum 默认为1，从第一张图片开始抽取
     * @param int $length 从 $startNum 开始抽取，共抽取 $length 张；默认为0，为0则抽取到最后
     * @param string $order 顺取倒取； 默认为 asc ，从正方向计数。 desc 从反方向计数
     * @param string|array $blacklist 图片地址黑名单，排除图片地址中包含该数据的地址；例如 传入 img.baidu.com  会排除 src="img.baidu.com/a.png"
     * @param string $model 抽取集合时，默认为正则模式；可选模式：preg  string，当 $length > 3 或者 $length = 0时，强制使用正则模式，因为取的数量大时，正则速度更快。
     * @return array 图片地址的集合数组，若无则返回空数组
     */
    public static function srcList($data, $startNum = 1, $length = 0, $order = 'asc', $blacklist = false, $model = 'string')
    {
        if($model === 'preg' || $length > 3 || $length === 0){
            $imgSrcArr = self::pregModel($data, [$startNum-1, $length, $blacklist], $order);
        }else{
            $imgSrcArr = [];
            for($i=$startNum; $i<$startNum+$length; $i++){
                $imgSrc = self::strModel($data, $i, $order);
                if(is_array($blacklist)){
                    $blackBool = true;
                    foreach ($blacklist as $k=>$v){
                        if (strpos($imgSrc, $blacklist) !== false) {
                            $blackBool = false;
                        }
                    }
                    if($blackBool){
                        $imgSrcArr[] = $imgSrc;
                    }else{
                        $length++;
                    }
                }else{
                    if ($blacklist === false || strpos($imgSrc, (string)$blacklist) === false) {
                        $imgSrcArr[] = $imgSrc;
                    }else{
                        $length++;
                    }
                }
            }
        }
        return $imgSrcArr;
    }

    /**
     * @param $str
     * @param $num
     * @param $order
     * @return bool|string|null
     */
    public static function strModel($str, $num, $order){

        $topStr = null;
        if($order != 'asc'){
            $funcStr = 'strrpos';
        }else{
            $funcStr = 'strpos';
        }
        for($i=1; $i<=$num; $i++){
            $firstNum = $funcStr($str, '<img');
            if($firstNum !== false){
                if($order != 'asc'){
                    $topStr = $str;
                    $str = substr($str, 0, $firstNum);
                }else{
                    $str = substr($str, $firstNum+4);
                }
            }else{
                return null;
            }
        }
        $str = $order=='asc'?$str:$topStr;
        $firstNum1 = $funcStr($str, 'src=');
        $type = substr($str, $firstNum1+4, 1);
        $str2 = substr($str, $firstNum1+5);
        if($type == '\''){
            $position = strpos($str2, "'");
        }else{
            $position = strpos($str2, '"');
        }
        return substr($str2, 0, $position);

    }

    /**
     * @param $str
     * @param $num
     * @param $order
     * @return string|array|null
     */
    public static function pregModel($str, $num, $order){

        preg_match_all("/<img.*>/isU", $str, $ereg);
        $img = $ereg[0];
        if($order != 'asc'){
            $img = array_reverse($img);
        };
        if(is_array($num)){
            $startNum = $num[0];
            $length = $num[1];
            $blacklist = $num[2];
            $imgSrcArr = [];
            foreach($img as $key=>$value){
                $imgSrc = $value;
                $pregModel='/src=(\'|")(.*)(?:\1)/isU';
                preg_match_all($pregModel, $imgSrc, $img1);
                if(is_array($blacklist)){
                    $blacklistBool = true;
                    foreach($blacklist as $v){
                        if(strpos($img1[2][0], $v) !== false){
                            $blacklistBool = false;
                        };
                    }
                    if($blacklistBool){
                        $imgSrcArr[] = $img1[2][0]??'';
                    }
                } else {
                    if ($blacklist === false || strpos($img1[2][0], (string)$blacklist) === false) {
                        $imgSrcArr[] = $img1[2][0]??'';
                    }
                };
            }
            if($length > 0){
                return array_slice($imgSrcArr, $startNum, $length);
            }else{
                return array_slice($imgSrcArr, $startNum);
            }
        }else{
            if(!empty($img[$num])){
                $imgStr = $img[$num];
                $pregModel='/src=(\'|")(.*)(?:\1)/isU';
                preg_match_all($pregModel, $imgStr, $img1);
                return $img1[2][0];
            }else{
                return null;
            }
        }
    }

    /**
     * 替换图片链接
     * @param $images
     * @param mixed $url
     * @return array|false|string[]
     */
    public static function replaceImageUrl($images,$url='')
    {
        if(!$images) return false;
        $url = $url ?: request()->domain();
        if(is_array($images) )
        {
            foreach ($images as $k=>$v) {
                if(!strstr( $v,'http') && !strstr( $v,'https'))
                {
                    $images[$k] = $url.$v;
                }
            }
        }else{
            if(!strstr( $images,'http') && !strstr( $images,'https'))
            {
                $images = $url.$images;
            }
        }
        return $images;
    }

    /**
     * 高清压缩图片
     * @param string $saveName 保存图片  提供图片名（可不带扩展名，用源图扩展名）用于保存。或不提供文件名直接显示
     * @param string $src 源图
     * @param float $percent  压缩比例
     */
    public static function compressImg(string $saveName='',string $src='', float $percent=0.7)
    {
        self::$src = $src;
        self::$percent = $percent;
        self::_openImage();
        return $saveName ? self::_saveImage($saveName):self::_showImage();
    }

    /**
     * 内部：打开图片
     */
    private static function _openImage()
    {
        list ($width, $height, $type, $attr) = getimagesize(self::$src);
        self::$imageinfo = array(
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_extension($type, false),
            'attr' => $attr
        );
        $fun = "imagecreatefrom" . self::$imageinfo['type'];
        self::$image = $fun(self::$src);
        self::_thumpImage();
    }

    /**
     * 内部：操作图片
     */
    private static function _thumpImage()
    {
        $new_width = self::$imageinfo['width']*self::$percent; // * $this->percent;
        $new_height = self::$imageinfo['height']*self::$percent; // * $this->percent;
        $image_thump = imagecreatetruecolor($new_width, $new_height);
        // 将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump, self::$image, 0, 0, 0, 0, $new_width, $new_height, self::$imageinfo['width'], self::$imageinfo['height']);
        imagedestroy(self::$image);
        self::$image = $image_thump;
    }

    /**
     * 输出图片:保存图片则用saveImage()
     */
    private static function _showImage()
    {
        header('Content-Type: image/' . self::$imageinfo['type']);
        $functions = "image" . self::$imageinfo['type'];
        return $functions(self::$image);
    }

    /**
     * 保存图片到硬盘：
     * @param string $dstImgName
     * 1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
     */
    private static function _saveImage(string $dstImgName)
    {
        if (empty($dstImgName))
            return false;
        $allowImgs = [
            '.jpg',
            '.jpeg',
            '.png',
            '.bmp',
            '.wbmp',
            '.gif'
        ]; // 如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $dstExt = strrchr($dstImgName, ".");
        $sourceExt = strrchr(self::$src, ".");
        if (!empty($dstExt))
            $dstExt = strtolower($dstExt);
        if (!empty($sourceExt))
            $sourceExt = strtolower($sourceExt);

        // 有指定目标名扩展名
        if (!empty($dstExt) && in_array($dstExt, $allowImgs)) {
            $dstName = $dstImgName;
        } elseif (!empty($sourceExt) && in_array($sourceExt, $allowImgs)) {
            $dstName = $dstImgName . $sourceExt;
        } else {
            $dstName = $dstImgName . self::$imageinfo['type'];
        }
        $functions = "image" . self::$imageinfo['type'];
        return $functions(self::$image, $dstName);
    }

    /**
     * 销毁图片
     */
    public function __destruct()
    {
        imagedestroy(self::$image);
    }
}