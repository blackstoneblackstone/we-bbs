<?php
namespace app\common\library\helper;
class CompressImg
{
    private $image;
    private $imageInfo;

  /** 高清压缩图片
   * @param string $saveName 提供图片名（可不带扩展名，用源图扩展名）用于保存。或不提供文件名直接显示
   */
  public function compress($src,$saveName='', $percent='0.8')
  {
      $this->_openImage($src,$percent);
      if(!empty($saveName)) $this->_saveImage($saveName,$src); //保存
      else $this->_showImage();
  }
  /**
   * 内部：打开图片
   */
  private function _openImage($src,$percent)
  {
      list($width, $height, $type, $attr) = getimagesize($src);
      $this->imageInfo = array(
          'width'=>$width,
          'height'=>$height,
          'type'=>image_type_to_extension($type,false),
          'attr'=>$attr
      );
      $fun = "imagecreatefrom".$this->imageInfo['type'];
      $this->image = $fun($src);
      $this->_thumpImage($percent);
  }
  /**
   * 内部：操作图片
   */
  private function _thumpImage($percent)
  {
      $new_width = $this->imageInfo['width'] * $percent;
      $new_height = $this->imageInfo['height'] * $percent;
      $image_thump = imagecreatetruecolor($new_width,$new_height);
      //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
      imagecopyresampled($image_thump,$this->image,null,null,null,null,$new_width,$new_height,$this->imageInfo['width'],$this->imageInfo['height']);
      imagedestroy($this->image);
      $this->image = $image_thump;
  }
  /**
   * 输出图片:保存图片则用saveImage()
   */
  private function _showImage()
  {
      header('Content-Type: image/'.$this->imageInfo['type']);
      $funcs = "image".$this->imageInfo['type'];
      $funcs($this->image);
  }
  /**
   * 保存图片到硬盘：
   * @param string $dstImgName 、可指定字符串不带后缀的名称，使用源图扩展名 。、直接指定目标图片名带扩展名。
   */
  private function _saveImage($dstImgName,$src)
  {
      if(empty($dstImgName)) return false;
      $allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp','.gif'];  //如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
      $dstExt = strrchr($dstImgName ,".");
      $sourseExt = strrchr($src ,".");
      if(!empty($dstExt)) $dstExt =strtolower($dstExt);
      if(!empty($sourseExt)) $sourseExt =strtolower($sourseExt);
      //有指定目标名扩展名
      if(!empty($dstExt) && in_array($dstExt,$allowImgs)){
          $dstName = $dstImgName;
      }elseif(!empty($sourseExt) && in_array($sourseExt,$allowImgs)){
          $dstName = $dstImgName.$sourseExt;
      }else{
          $dstName = $dstImgName.$this->imageInfo['type'];
      }
      $funcs = "image".$this->imageInfo['type'];
      $funcs($this->image,$dstName);
  }

  /**
   * 销毁图片
   */
  public function __destruct(){
      if($this->image)
      {
          imagedestroy($this->image);
      }
  }
}