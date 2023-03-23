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
require_once AWS_PATH.'Services/Picture/src/autoloader.php';
use Grafika\Grafika;
use Grafika\Color;
class core_picture
{
	protected $editor;

	public function __construct()
	{
		$this->editor = Grafika::createEditor();
	}

    /**
     * 图像裁切
     * @param string $source_img 原图片
     * @param string $out_img 输出图片
     * @param int $width 缩略图宽度
     * @param int $height 缩略图高度
     * @param string $type 裁切类型
     * @return mixed
     */
	public function resize($source_img,$out_img,$width,$height,$type='auto')
	{
        $this->editor->open($image , $source_img);
        switch ($type)
        {
            //等比例缩放
            case 'fit':
                $this->editor->resizeFit($image , $width , $height);
                break;
            //固定比例缩放
            case 'exact':
                $this->editor->resizeExact($image , $width , $height);
                break;
            //居中裁切
            case 'fill':
                $this->editor->resizeFill($image , $width , $height);
                break;
            //等宽缩放
            case 'exact_width':
                $this->editor->resizeExactWidth($image , $width);
                break;
            //等宽缩放
            case 'exact_height':
                $this->editor->resizeExactHeight($image , $height);
                break;
            default:
                $this->editor->crop( $image, $width, $height, 'smart' );
                break;
        }

        $this->editor->save($image , $out_img);

        return $out_img;
	}

    /**
     * 图像水印
     * @param string $type 水印类型
     * @return mixed
     */
    public function watermark($source_img,$type='text')
    {
        try {
            $upload_dir = str_replace('\\','/',get_setting('upload_dir'));
            $upload_dir = explode('/',$upload_dir);
            $upload_dir = end($upload_dir);

            if(strstr($source_img, get_setting('upload_url')))
            {
                $source_img = str_replace(get_setting('upload_url'),'/'.$upload_dir,$source_img);
            }

            if(strstr($source_img, '_water'))
            {
                return $source_img;
            }

            $source_img = str_replace('/'.$upload_dir,get_setting('upload_dir'),$source_img);

            $this->editor->open($image, $source_img);

            switch ($type)
            {
                //图片合并
                case 'image':
                    if(get_setting('watermark_image'))
                    {
                        $watermark_image = get_setting('watermark_image');

                        if(strstr($watermark_image, get_setting('upload_url')))
                        {
                            $watermark_image = str_replace(get_setting('upload_url'),'/'.$upload_dir,$watermark_image);
                        }

                        $watermark_image = str_replace('/'.$upload_dir,get_setting('upload_dir'),$watermark_image);

                        $this->editor->open($img,$watermark_image);
                        $watermark_image_opacity = intval(get_setting('watermark_image_opacity'))>1 ? intval(get_setting('watermark_image_opacity'))*0.1 : 1;
                        $this->editor->blend($image,$img,get_setting('watermark_image_type'),$watermark_image_opacity ,get_setting('watermark_image_position'));
                    }
                break;
                //图片写文字
                default:
                    if(get_setting('watermark_text'))
                    {
                        $watermark_text_font_path = AWS_PATH.'Services'.DIRECTORY_SEPARATOR.'Picture'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'msyh.ttf';
                        $font =  get_setting('watermark_text_font') ?? $watermark_text_font_path;
                        $this->editor->text($image, get_setting('watermark_text'), get_setting('watermark_text_font_size'), get_setting('watermark_text_x'), get_setting('watermark_text_y'),new Color(get_setting('watermark_text_color')),$font, get_setting('watermark_text_angle'));
                    }
                break;
            }

            //重新命名图片名称
            $source_arr = explode('.',$source_img);
            $des = end($source_arr);
            array_pop($source_arr);
            $source_img = implode('.',$source_arr).'_water'.'.'.$des;
            $this->editor->save($image, $source_img);

            //去掉绝对目录
            return str_replace(get_setting('upload_dir'),get_setting('upload_url'),$source_img);
        } catch (Exception $e) {
        }
    }
}