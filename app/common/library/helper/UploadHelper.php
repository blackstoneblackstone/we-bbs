<?php
namespace app\common\library\helper;
use think\facade\Filesystem;
use think\file\UploadedFile;

class UploadHelper
{
    // 上传验证规则
    protected $uploadValidate = [];
    protected $attach_url;
    protected $attach_path;
    protected $file_type;
    protected $file_mime;
    protected $file_size;
    protected $file_name;
    protected $file_md5;
    protected $file_sha1;
    protected $allowed_types;
    protected $uploadPath='common';
    protected $access_key;
    protected $file_ext;
    protected $uploadMethod;
    protected $file;
    protected $org_file;
    protected $upload_type='img';
    protected $error;
    protected static $instance;

    // 构造方法
    public function __construct()
    {
        //附件外部url
        $this->attach_url = config('filesystem.disks.public.url').'/';
        //附件存储地址
        $this->attach_path = config('filesystem.disks.public.root');
    }

    /**
     * 初始化
     * @return UploadHelper
     */
    public static function instance(): UploadHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 设置上传路径
     * @param $uploadPath
     * @return $this
     */
    public function setUploadPath($uploadPath): UploadHelper
    {
        $this->uploadPath= $this->cleanFileName($uploadPath);
        return $this;
    }

    /**
     * 设置access_key
     * @param $access_key
     * @return $this
     */
    public function setAccessKey($access_key): UploadHelper
    {
        $this->access_key= $access_key;
        return $this;
    }

    /**
     * 设置上传类型
     * @param $type
     * @return $this
     */
    public function setUploadType($type): UploadHelper
    {
        $type = is_array($type)?sqlFilter(end($type)) : sqlFilter($type);
        $type = StringHelper::filterWords($type);
        if(!in_array($type,['img','file']))
            $type = 'img';
        $this->upload_type = $type;
        return $this;
    }

    /**
     * 上传检测
     * @param string $field 文件字段 //  $data_stream 有值时为文件名
     * @param mixed $data_stream 文件流
     * @return bool
     */
    public function check(string $field='',$data_stream=''): bool
    {
        if($data_stream)
        {
            $tmp_file = runtime_path('storage') . 'xhr_' . md5($field . microtime(TRUE) . rand(1, 999)) . '.stream';
            if(!is_dir(runtime_path('storage')))
            {
                FileHelper::mkDirs(runtime_path('storage'));
            }
            if (!file_put_contents($tmp_file, $data_stream))
            {
                return FALSE;
            }
            $this->uploadValidate = $this->uploadVal();
            $fileSystem = ($field && $tmp_file) ? new UploadedFile($tmp_file,$field) : '';
            $this->file = $fileSystem;
        }else{
            $this->uploadValidate = $this->uploadVal();
            $this->file = $field ? request()->file($field) : (request()->file('file')?:request()->file('aw-upload-file'));
        }
        if(!$this->file)
        {
            return false;
        }

        $this->uploadPath = $this->cleanFileName($this->uploadPath);
        $this->file_size = $this->file->getSize();
        $this->file_name = $this->cleanFileName($this->file->getOriginalName());
        $this->file_md5 = $this->file->md5();
        $this->file_sha1 = $this->file->sha1();
        $this->file_mime = $this->file->getMime();
        $this->file_ext = $this->file->extension();
        if(!$this->is_allowed_filetype())
        {
            return false;
        }
        return true;
    }

    /**
     * 开始上传
     * @param $uid
     * @param string $field 文件字段 //  $data_stream 有值时为文件名
     * @param mixed $data_stream 文件流
     * @return array
     */
    public function upload($uid,string $field='',$data_stream=''): array
    {
        //上传检查
        if(!$this->check($field,$data_stream))
        {
            $result['url'] = '';
            $result['msg'] = $this->error;
            $result['code'] = 0;
            $result["errno"] = 1;
            $result['error'] = 1;
            $result['location'] = '';
            $result['is_image'] = 0;
            $result['file_name'] = $this->file_name;
            $result['state'] = 'ERROR'; //兼容百度
            return $result;
        }

        if (get_setting('upload_type') == 'tp') {
            $result = $this->uploadFile($uid);
        }else{
            $result = $this->bigUpload($uid);
        }

        return $result;
    }

    // 上传验证规则
    public function uploadVal(): array
    {
        $fileSize = 0;
        if ($this->upload_type == 'file') {
            // 文件限制
            if (get_setting('upload_file_ext')) {
                $fileExt = $this->removeExt(get_setting('upload_file_ext'));
            } else {
                $fileExt = 'rar,zip,avi,rmvb,3gp,flv,mp3,mp4,txt,doc,xls,ppt,pdf,xls,docx,xlsx,doc';
            }

            // 限制文件大小(单位b)
            if (get_setting('upload_file_size')) {
                $fileSize = get_setting('upload_file_size') * 1024;
                $message = [
                    'file.fileExt'=>'只允许上传:'.$fileExt.'后缀的文件类型',
                    'file.fileSize'=>'上传文件大小不能超过'.($fileSize/1024).'KB',
                ];
                $file = ['file'=>'fileSize:'.$fileSize.'|fileExt:'.$fileExt];
            }else{
                $message = [
                    'file.fileExt'=>'只允许上传:'.$fileExt.'后缀的文件类型',
                ];
                $file = ['file'=>'fileExt:'.$fileExt];
            }
        } else {
            // 图片限制
            if (get_setting('upload_image_ext')) {
                $fileExt = $this->removeExt(get_setting('upload_image_ext'));
            } else {
                $fileExt = 'jpg,png,gif,jpeg';
            }

            // 限制图片大小(单位b)
            if (get_setting('upload_image_size')) {
                $fileSize = get_setting('upload_image_size') * 1024;
                $message = [
                    'image.fileExt'=>'只允许上传:'.$fileExt.'后缀的图片类型',
                    'image.fileSize'=>'上传图片大小不能超过'.($fileSize/1024).'KB',
                ];
                $file = ['image'=>'fileSize:'.$fileSize.'|fileExt:'.$fileExt];
            }else{
                $message = [
                    'image.fileExt'=>'只允许上传:'.$fileExt.'后缀的图片类型',
                ];
                $file = ['image'=>'fileExt:'.$fileExt];
            }
        }

        $this->allowed_types = explode(',',$fileExt);
        return ['rule'=>$file,'message'=>$message];
    }

    // tp上传文件
    public function uploadFile($uid): array
    {
        $this->access_key = $this->access_key ?: md5($uid.time());
        $validate = $this->uploadValidate;
        try {
            validate($validate['rule'],$validate['message'])->check(['file' => $this->file]);
            $saveName = Filesystem::disk('public')->putFile($this->uploadPath, $this->file);
            if ($saveName) {
                $saveName = str_replace(DS, '\\', $saveName);
                $file_ext =  strtolower(substr($saveName, strrpos($saveName, '.') + 1));
                $width = $height = 0;
                $url = $this->attach_url.str_replace('\\','/',$saveName);
                $thumb = $url;
                $isImage = self::isImage($this->file_ext, $this->file_mime) ? 1 : 0;
                $path = str_replace('\\',DS,$this->attach_path.DS.$saveName);
                // 图片宽高
                if ($isImage) {
                    if ($image = getimagesize($path)) {
                        preg_match_all('/"(\d+)"/', $image[3], $wh);
                        $width = (int) $wh[1][0];
                        $height = (int) $wh[1][1];
                    }

                    //图片做压缩处理
                    if(get_setting('upload_image_thumb_enable','N')=='Y' && get_setting('upload_image_thumb_percent')< 1 && get_setting('upload_image_thumb_percent')>0 && $file_ext!='gif' && $file_ext!='GIF')
                    {
                        $thumb_path = str_replace('.'.$file_ext,'.thumb.'.$file_ext,$path);
                        ImageHelper::compressImg($thumb_path,$path,get_setting('upload_image_thumb_percent'));
                        if(file_exists($thumb_path))
                        {
                            $thumb = str_replace('.'.$file_ext,'.thumb.'.$file_ext,$url);
                        }
                    }
                }
                $data = [
                    'uid'=>$uid,
                    'name'=>$this->file_name,
                    'path' => $path,
                    'thumb'=>$thumb,
                    'url'=> $url,
                    'ext'=>$file_ext,
                    'size'=>$this->file_size/1024,
                    'width'=>$width,
                    'height'=>$height,
                    'md5'=>$this->file_md5,
                    'sha1'=>$this->file_sha1,
                    'mime'=>$this->file_mime,
                    'access_key'=>$this->access_key,
                    'item_type'=>$this->uploadPath,
                    'driver'=>'local',
                ];
                $attach_id = db('attach')->insertGetId($data);

                $result['state'] = 'SUCCESS'; //兼容百度
                $result['code'] = 1;
                $result["url"] = $thumb;
                $result["data"]["{$this->file_name}"] = $thumb;
                $result["location"] = $thumb;
                $result["errno"] = 0;
                $result['error'] = 1;
                $result['attach_id'] = $attach_id;
                $result['access_key'] = $this->access_key;
                $result['msg'] = L('上传成功');
                $result['is_image'] = $isImage;
                $result['file_name'] = $this->file_name;
                // 图片信息
                if ($isImage) {
                    $result['width'] = $width;
                    $result['height'] = $height;
                    $result['size'] = $data['size'];
                }

                //上传后钩子
                if($hook_result = hook('attach_upload_after',['data'=>$data,'attach_id'=>$attach_id,'result'=>$result])) {
                    return json_decode($hook_result,true);
                }
                return $result;
            }

            $result['url'] = '';
            $result['msg'] = '上传不正确';
            $result['code'] = 0;
            $result["errno"] = 1;
            $result['error'] = 1;
            $result['attach_id'] = 0;
            $result['location '] = '';
            $result['is_image'] =0;
            $result['state'] = 'ERROR'; //兼容百度
            $result['file_name'] = $this->file_name;
            return $result;
        } catch (\Exception $e) {
            $result['url'] = '';
            $result['msg'] = $e->getMessage();
            $result['code'] = 0;
            $result["errno"] = 1;
            $result['error'] = 1;
            $result['attach_id'] = 0;
            $result['location '] = '';
            $result['is_image']=0;
            $result['state'] = 'ERROR'; //兼容百度
            $result['file_name'] = $this->file_name;
            return $result;
        }
    }

    // 大文件切片上传
    public function bigUpload($uid): array
    {
        $this->access_key = $this->access_key ? : md5($uid.time());
        try {
            $validate = $this->uploadValidate;
            validate($validate['rule'],$validate['message'])->check(['file' => $this->file]);
        } catch (\Exception $e) {
            $result['url'] = '';
            $result["location"] ='';
            $result['msg'] = $e->getMessage();
            $result['code'] = 0;
            $result["errno"] = 1;
            $result['error'] = 1;
            $result['attach_id'] = 0;
            $result['state'] = 'ERROR'; //兼容百度
            $result['is_image'] =0;
            $result['file_name'] = $this->file_name;
            return $result;
        }
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }
        if (!empty($_REQUEST['debug'])) {
            $random = rand(0, intval($_REQUEST['debug']));
            if ($random === 0) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }
        // 页面执行时间不限制
        @set_time_limit(5 * 60);
        // 设置临时上传目录
        $targetDir = runtime_path('storage');
        // 设置上传目录
        $uploadDir = $this->attach_path . DS . $this->uploadPath . DS . date('Ymd');
        // 上传完后清空临时目录
        $cleanupTargetDir = true;
        // 临时文件期限
        $maxFileAge = 5 * 3600;
        // 创建临时目录
        if(!file_exists($targetDir) &&  !is_dir($targetDir))
        {
            FileHelper::mkDirs($targetDir);
        }

        // 创建上传目录
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            FileHelper::mkDirs($uploadDir);
        }

        // 获取上传文件名称
        $fileName = $this->file->getOriginalName();
        //$fileName = iconv('UTF-8', 'gb2312', $fileName);
        // 临时上传完整目录信息
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // 定义命名规则
        $pathInfo = pathinfo($fileName);
        // md5
        $fileName = md5(time() . $pathInfo['basename']) . '.' . $pathInfo['extension'];

        // 正式上传完整目录信息
        $uploadFullPath = $uploadDir . DS . $fileName;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        // 清空临时目录
        if ($cleanupTargetDir)
        {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open temp directory';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result["location"] ='';
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                $result['is_image'] =0;
                $result['file_name'] = $this->file_name;
                return$result;
            }

            while (($file = readdir($dir)) !== false) {
                $tmpFilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // 如果临时文件是当前文件，则转到下一个
                if ($tmpFilePath == "{$filePath}_{$chunk}.part" || $tmpFilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                // 如果临时文件早于最大使用期限并且不是当前文件，则将其删除
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpFilePath) < time() - $maxFileAge)) {
                    @unlink($tmpFilePath);
                }
            }
            closedir($dir);
        }

        // 打开临时文件
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            $result['url'] = '';
            $result['msg'] = 'Failed to open output stream';
            $result["location"] ='';
            $result['code'] = 0;
            $result["errno"] = 1;
            $result['error'] = 1;
            $result['state'] = 'ERROR'; //兼容百度
            $result['is_image'] =0;
            $result['file_name'] = $this->file_name;
            return$result;
        }

        if ($this->file) {
           /* if (!is_uploaded_file($this->file->getRealPath())) {
                $result['url'] = '';
                $result['msg'] = 'Failed to move uploaded file';
                $result["location"] ='';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                return$result;
            }*/

            // 读取二进制输入流并将其附加到临时文件
            if (!$in = @fopen($this->file->getRealPath(), "rb")) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open input stream';
                $result["location"] ='';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['is_image'] =0;
                $result['state'] = 'ERROR'; //兼容百度
                $result['file_name'] = $this->file_name;
                return $result;
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open input stream';
                $result["location"] ='';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                $result['is_image'] =0;
                $result['file_name'] = $this->file_name;
                return$result;
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

        $done = true;
        for ($index = 0; $index < $chunks; $index++) {
            if (!file_exists("{$filePath}_{$index}.part")) {
                $done = false;
                break;
            }
        }
        if ($done) {
            if (!$out = @fopen($uploadFullPath, "wb")) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open output stream';
                $result["location"] ='';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['attach_id'] = 0;
                $result['state'] = 'ERROR'; //兼容百度
                $result['is_image'] =0;
                $result['file_name'] = $this->file_name;
                return$result;
            }
            if (flock($out, LOCK_EX)) {
                for ($index = 0; $index < $chunks; $index++) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            @fclose($out);
            $url = $this->attach_url.$this->uploadPath . '/' . date('Ymd').'/'.$fileName;
            $width=$height=0;
            $thumb = $url;
            $isImage = self::isImage($this->file_ext, $this->file_mime) ? 1 : 0;
            $path = str_replace('\\',DS,$uploadFullPath);

            // 图片宽高
            if ($isImage) {
                if ($image = getimagesize($path)) {
                    preg_match_all('/"(\d+)"/', $image[3], $wh);
                    $width = (int) $wh[1][0];
                    $height = (int) $wh[1][1];
                }

                //图片做压缩处理
                if(get_setting('upload_image_thumb_enable','N')=='Y' && get_setting('upload_image_thumb_percent')< 1 && get_setting('upload_image_thumb_percent')>0 && $pathInfo['extension']!='gif' && $pathInfo['extension']!='GIF')
                {
                    $thumb_path = str_replace('.'.$pathInfo['extension'],'.thumb.'.$pathInfo['extension'],$path);
                    ImageHelper::compressImg($thumb_path,$path,get_setting('upload_image_thumb_percent'));
                    if(file_exists($thumb_path))
                    {
                         $thumb = str_replace('.'.$pathInfo['extension'],'.thumb.'.$pathInfo['extension'],$url);
                    }
                }
            }

            $_data = [
                'uid'=>$uid,
                'name'=>$fileName,
                'path'=> $path,
                'thumb'=>$thumb,
                'url'=> $url,
                'ext'=>$pathInfo['extension'],
                'size'=>$this->file_size/1024,
                'width'=>$width,
                'height'=>$height,
                'md5'=>$this->file_md5,
                'sha1'=>$this->file_sha1,
                'mime'=>$this->file_mime,
                'access_key'=>$this->access_key,
                'item_type'=>$this->uploadPath,
                'driver'=>'local',
            ];
            $attach_id = db('attach')->insertGetId($_data);

            $result['state'] = 'SUCCESS'; //兼容百度
            $result['code'] = 1;
            $result["url"] = $url;
            $result["location"] =$url;
            $result["data"]["{$fileName}"] = $url;
            $result["errno"] = 0;
            $result['error'] = 0;
            $result['attach_id'] = $attach_id;
            $result['msg'] = lang('上传成功');
            $result['is_image'] = $isImage;
            $result['file_name'] = $this->file_name;
            // 图片信息
            if ($isImage) {
                $result['width'] = $width;
                $result['height'] = $height;
                $result['size'] = $_data['size'];
            }

            //上传后钩子
            if($hook_result = hook('attach_upload_after',['data'=>$_data,'attach_id'=>$attach_id,'result'=>$result])) {
                return json_decode($hook_result,true);
            }

            return $result;
        }

        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    // 移除上传危险后缀
    public function removeExt(string $ext = ''): string
    {
        $ext = strtolower($ext);
        if (strpos($ext, 'php') !== false) {
            $ext = str_ireplace("php", "", $ext);
            return $this->removeExt($ext);
        }
        if (strpos($ext, 'asp') !== false) {
            $ext = str_ireplace("asp", "", $ext);
            return $this->removeExt($ext);
        }

        if (strpos($ext, 'sh') !== false) {
            $ext = str_ireplace("sh", "", $ext);
            return $this->removeExt($ext);
        }
        return $ext;
    }

    //是否是图片
    public function isImage($file_ext,$file_mime): bool
    {
        if (!in_array(strtolower($file_ext), array(
            'jpg',
            'jpe',
            'jpeg',
            'bmp',
            'gif',
            'png'
        ))) {
            return FALSE;
        }

        $png_mimes = array('image/x-png');
        $jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

        if (in_array($file_mime, $png_mimes)) {
            $file_mime = 'image/png';
        }

        if (in_array($file_mime, $jpeg_mimes)) {
            $file_mime = 'image/jpeg';
        }

        $img_mimes = array(
            'image/gif',
            'image/jpeg',
            'image/png',
        );
        return in_array($file_mime, $img_mimes, TRUE);
    }

    //移除风险文件名
    public function cleanFileName($filename): string
    {
        $bad = array(
            "<!--",
            "-->",
            "'",
            "<",
            ">",
            '"',
            '&',
            '$',
            '=',
            ';',
            '?',
            '/',
            "%20",
            "%22",
            "%3c",      // <
            "%253c",    // <
            "%3e",      // >
            "%0e",      // >
            "%28",      // (
            "%29",      // )
            "%2528",    // (
            "%26",      // &
            "%24",      // $
            "%3f",      // ?
            "%3b",      // ;
            "%3d"       // =
        );
        $filename = is_array($filename) ?end($filename) : $filename;
        $filename = str_replace($bad, '', $filename);
        return stripslashes(sqlFilter($filename));
    }

    //是否是允许的上传文件类型
    public function is_allowed_filetype(): bool
    {
        if (in_array('*',$this->allowed_types) || !$this->allowed_types)
        {
            return TRUE;
        }

        if (count($this->allowed_types) == 0 OR !is_array($this->allowed_types))
        {
            $this->error = '上传文件类型不允许';
            return FALSE;
        }
        $defend_ext = ['php','js','sh','exe','html','htm'];
        $ext = strtolower(ltrim($this->file_ext, '.'));
        if(in_array($ext,$defend_ext))
        {
            $this->error = '上传文件类型不允许';
            return FALSE;
        }

        if (!in_array($ext, $this->allowed_types))
        {
            $this->error = '上传文件类型不允许';
            return FALSE;
        }
        $image_types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');

        if (in_array($ext, $image_types))
        {
            if(!$this->isImage($this->file_ext,$this->file_mime))
            {
                $this->error = '上传文件类型不允许';
                return  false;
            }
        }
        return TRUE;
    }
}