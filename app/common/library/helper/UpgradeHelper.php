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
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;

/**
 * Class UpgradeHelper
 * @package app\common\library\helper
 */
class UpgradeHelper
{
    protected static $instance;
    const API_URL = 'https://wenda.wecenter.com/plugins/cloud/';
    public static function instance(): UpgradeHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    // 检测是否有新版本
    public function checkVersion(): array
    {
        $server = self::apiRequest('cloud/check');
        if(!$server['code'])
        {
            return [
                'code'=>0,
                'msg'=>'通信异常',
                'data'=>''
            ];
        }
        $data = $server['data'];
        if($data)
        {
            if (self::compareVersion($data['version'],config('version.version'))==1) {
                $result= [
                    'code'=>200,
                    'msg'=>'有新版本',
                    'data'=>$data
                ];
            } else {
                $result= [
                    'code'=>201,
                    'msg'=>'已是最新',
                    'data'=>$data
                ];
            }
        }else{
            $result= [
                'code'=>0,
                'msg'=>'通信异常',
                'data'=>''
            ];
        }
        return $result;
    }

    /**
     * 解压缩
     * @param string $file 要解压的文件
     * @param string $to_dir 要存放的目录
     * @return int
     */
    public function dealZip(string $file, string $to_dir): int
    {
        if (trim($file) == '') {
            return 406;
        }
        if (trim($to_dir) == '') {
            return 406;
        }
        $zip = new \ZipArchive;
        // 中文文件名要使用ANSI编码的文件格式
        if ($zip->open($file) === TRUE) {
            //提取全部文件
            $zip->extractTo($to_dir);
            $zip->close();
            $result = 200;
        } else {
            $result = 406;
        }
        return $result;
    }

    /**
     * 遍历当前目录不包含下级目录
     * @param string $dir 要遍历的目录
     * @param string $file 要过滤的文件
     * @return array|false
     */
    public function scanDir(string $dir, string $file='')
    {
        if (trim($dir) == '') {
            return false;
        }
        $file_arr = scandir($dir);
        $new_arr = [];
        foreach($file_arr as $item){
            if($item!=".." && $item !="." && $item != $file){
                $new_arr[] = $item;
            }
        }
        return $new_arr;
    }

    /**
     * 合并目录且只覆盖不一致的文件
     * @param string $source 要合并的文件夹
     * @param string $target 要合并的目的地
     * @return array|false 处理的文件数
     */
    public function copyMerge(string $source, string $target,$backup_dir='') {
        if (trim($source) == '') {
            return false;
        }
        if (trim($target) == '') {
            return false;
        }
        // 路径处理
        $source = preg_replace ( '#/\\\\#', DIRECTORY_SEPARATOR, $source );
        $target = preg_replace ( '#\/#', DIRECTORY_SEPARATOR, $target );
        $backup_dir = preg_replace ( '#\/#', DIRECTORY_SEPARATOR, $backup_dir );
        $source = rtrim ( $source, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        $target = rtrim ( $target, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        $backup_dir = rtrim ( $backup_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        // 记录处理了多少文件
        $count = 0;
        // 如果目标目录不存在，则创建。
        if (! is_dir ( $target )) {
            mkdir ( $target, 0777, true );
            $count ++;
        }
        if (! is_dir ( $backup_dir )) {
            mkdir ( $backup_dir, 0777, true );
        }

        // 搜索目录下的所有文件
        foreach ( glob ( $source . '*' ) as $filename ) {
            if (is_dir ( $filename )) {
                // 如果是目录，递归合并子目录下的文件。
                $count += $this->copyMerge( $filename, $target . basename ( $filename ),$backup_dir. basename ( $filename ));
            } elseif (is_file ( $filename )) {
                // 如果是文件，判断当前文件与目标文件是否一样，不一样则拷贝覆盖。
                // 这里使用的是文件md5进行的一致性判断，可靠但性能低。
                if (! file_exists ( $target . basename ($filename)) || md5 ($filename) != md5 ($target . basename ($filename)) || filesize($filename) != filesize($target . basename ($filename))) {
                    if(file_exists($target . basename ( $filename )))
                    {
                        copy($target . basename ( $filename ),$backup_dir . basename ( $filename ));
                    }

                    copy ( $filename, $target . basename ( $filename ) );
                    $count ++;
                }
            }
        }

        // 返回处理了多少个文件
        return $count;
    }

    /**
     * 遍历删除文件
     * @param string $dir 要删除的目录
     * @return bool 成功与否
     */
    public function delDir(string $dir): bool
    {
        if (trim($dir) == '') {
            return false;
        }
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullPath=$dir."/".$file;
                if(!is_dir($fullPath)) {
                    unlink($fullPath);
                } else {
                    $this-> delDir($fullPath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 执行sql文件
     * @param string $dir 要执行的目录
     * @return array 成功与否
     */
    public function fetchSql(string $dir): array
    {
        if (trim($dir) == '') {
            return  [
                'code'=>1,
                'msg'=>'',
                'data'=>''
            ];
        }
        $sqlFile = $dir.'update.sql';
        $prefix  =  config('database.connections.mysql.prefix');
        db()->startTrans();
        try {
            if (is_file($sqlFile)) {
                $lines = file($sqlFile);
                $tempLine = '';
                foreach ($lines as $line) {
                    if (strpos($line, '--') === 0 || $line == '' || strpos($line, '/*') === 0) {
                        continue;
                    }
                    $tempLine .= $line;
                    if (substr(trim($line), -1, 1) == ';') {
                        // 不区分大小写替换前缀
                        $tempLine = str_ireplace('aws_', $prefix, $tempLine);
                        // 忽略数据库中已经存在的数据
                        $tempLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tempLine);
                        $sql_res = db()->execute($tempLine);
                        /*if (empty($sql_res)) {
                            return  [
                                'code'=>0,
                                'msg'=>'SQL执行失败：'.$tempLine,
                                'data'=>''
                            ];
                        }*/
                        $tempLine = '';
                    }
                }
            }
            db()->commit();
        }catch (\Exception $e)
        {
            db()->rollback();
            return  [
                'code'=>0,
                'msg'=>$e->getMessage(),
                'data'=>''
            ];
        }
        return  [
            'code'=>1,
            'msg'=>'',
            'data'=>''
        ];
    }

    /**
     * 下载程序压缩包文件
     * @param string $file_name
     * @param string $save_dir 要存放的目录
     * @param $hash
     * @return array|false
     */
    public function downFile(string $file_name, string $save_dir,$hash)
    {
        if (trim($file_name) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            return false;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        $filename = basename($file_name);
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //开始下载
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $hash);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        // 判断执行结果
        if ($status['http_code'] ==200) {
            $size = strlen($content);
            //文件大小
            $fp2 = @fopen($save_dir . $filename , 'a');
            fwrite($fp2, $content);
            fclose($fp2);
            unset($content, $url);
            $res = [
                'code' =>1 ,
                'file_name' => $filename,
                'save_path' => $save_dir . $filename
            ];
        } else {
            $res = false;
        }

        return $res;
    }

    /**
     * 获取文件内容
     * @param $url
     * @return false|string
     */
    public function getFile($url)
    {
        if (trim($url) == '') {
            return false;
        }
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'timeout'=>3,//单位秒
            )
        );
        $cnt=0;
        while($cnt<3 && ($res=@file_get_contents($url, false, stream_context_create($opts)))===FALSE) $cnt++;
        if ($res === false) {
            return false;
        } else {
            return $res;
        }
    }

    /**
     * @param $url
     * @param null $data
     * @param bool $json
     * @return array
     */
    public static function apiRequest($url,$data = null,bool $json=false): array
    {
        $curl = curl_init();    // curl 设置
        curl_setopt($curl, CURLOPT_URL, self::API_URL.$url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  // 校验证书节点
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);// 校验证书主机
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.98 Safari/537.36");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $header = [
            'ServerAuth: '.authCode(get_setting('authorize_code',0),'ENCODE','we'),
            'ServerBuild: ' . config('version.build'),
            'ServerVersion: ' . config('version.version'),
            'ServerDomain:'.Request::host(),
            'Expect:',
            'CLIENT-IP:'. getServerIp()
        ];
        if($json)
        {
            $header=array_merge($header,[
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        if($res === false)
        {
            return [
                'code'=>0,
                'msg'=>curl_error($curl),
                'data'=>[]
            ];
        }
        curl_close($curl);
        return [
            'code'=>1,
            'msg'=>'',
            'data'=>json_decode($res,true)
        ];
    }

    //云平台绑定
    public function bind($user_name,$password): array
    {
        if(!$user_name || !$password)
        {
            return [
                'code'=>0,
                'msg'=>'信息不正确',
                'data'=>['msg'=>'信息不正确']
            ];
        }

        return self::apiRequest('cloud/bind',[
            'username'=>$user_name,
            'password'=>$password
        ]);
    }

    //云平台解除绑定
    public function unbind(): array
    {
        return self::apiRequest('cloud/unbind');
    }

    // 在线更新
    public function update(): array
    {
        $base_dir = root_path();
        // 本地更新路径
        $local_up_dir = root_path().'update'.DS;
        // 本地缓存路径
        $cache_dir = runtime_path('update');
        // 没有就创建
        if(!is_dir($cache_dir)){
            mkdir($cache_dir,0777,true);
        }
        // 设定缓存目录名称
        $result = [];
        //下载更新包列表
        $server = self::apiRequest('cloud/lists');
        if (!$server['code'] || !$server['data']) {
            $result = [
                'code'=>0,
                'msg'=>'服务器更新文件获取失败',
                'data'=>''
            ];
        }else{
            $download_list = $server['data'];
            foreach ($download_list as $k=>$v)
            {
                $local = config('version');
                if ($local === false) {
                    self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>0,'msg'=>'本地版本信息获取失败']);
                    $result = [
                        'code'=>0,
                        'msg'=>'本地版本信息获取失败',
                        'data'=>''
                    ];
                }else{
                    if (self::versionToInteger($local['version']) < self::versionToInteger($v['version']))
                    {
                        try {
                            $down_res = $this->downFile($v['version'].'.zip',$local_up_dir,$v['hash']);
                            if(!$down_res || !$down_res['code'])
                            {
                                $result = [
                                    'code'=>0,
                                    'msg'=>'文件下载失败',
                                    'data'=>''
                                ];
                                self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>0,'msg'=>'文件下载失败']);
                            }else{
                                //下载成功 解压缩
                                if(!is_dir($cache_dir.'sql'.DS))
                                {
                                    mkdir($cache_dir.'sql'.DS,0777,true);
                                }

                                if(!is_dir($cache_dir.'script'.DS))
                                {
                                    mkdir($cache_dir.'script'.DS,0777,true);
                                }

                                if(!is_dir($cache_dir.'program'.DS))
                                {
                                    mkdir($cache_dir.'program'.DS,0777,true);
                                }

                                $zip_res = $this->dealZip($down_res['save_path'] ,$cache_dir);
                                // 判断解压是否成功
                                if ($zip_res == 406) {
                                    self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>0,'msg'=>'文件解压缩失败']);
                                    $result = [
                                        'code'=>0,
                                        'msg'=>'文件解压缩失败',
                                        'data'=>''
                                    ];
                                } else {
                                    //sql执行成功才进行文件替换
                                    $sqlRes = $this->fetchSql($cache_dir.'sql'.DS);
                                    if($sqlRes['code'])
                                    {
                                        //执行升级脚本
                                        if(is_dir($cache_dir.'program'.DS) && is_file($cache_dir.'program'.DS.'program.php'))
                                        {
                                            require $cache_dir.'program'.DS.'program.php';
                                        }

                                        // php文件合并 返回处理的文件数
                                        $file_up_res = $this->copyMerge($cache_dir.'script\\',$base_dir,$local_up_dir.'backup'.DS.$local['version'].DS);

                                        if (empty($file_up_res)) {
                                            self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>0,'msg'=>'文件移动合并失败']);
                                            $result = [
                                                'code'=>0,
                                                'msg'=>'文件移动合并失败',
                                                'data'=>''
                                            ];
                                        }else{
                                            // 更新完改写网站本地版本号
                                            $string = "<?php\r\n return ".var_export(['version'=>$v['version'],'build'=>$v['build']], true).';';
                                            $write_res = file_put_contents(config_path().'version.php',$string);
                                            if (empty($write_res)) {
                                                self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>0,'msg'=>'本地更新日志改写失败']);
                                                $result = [
                                                    'code'=>0,
                                                    'msg'=>'本地更新日志改写失败',
                                                    'data'=>''
                                                ];
                                            }else{
                                                // 删除临时文件
                                                $del_res = $this->delDir($cache_dir);
                                                if (empty($del_res)) {
                                                    self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>0,'msg'=>'删除临时缓存文件删除失败']);
                                                    $result = [
                                                        'code'=>0,
                                                        'msg'=>'删除临时缓存文件删除失败',
                                                        'data'=>''
                                                    ];
                                                }else{
                                                    self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>1,'msg'=>'在线升级已完成']);
                                                    $result = [
                                                        'code'=>1,
                                                        'msg'=>'在线升级已完成',
                                                        'data'=>''
                                                    ];
                                                }
                                            }
                                        }
                                    }else{
                                        $result = $sqlRes;
                                    }
                                }
                            }
                        }catch (\Exception $e)
                        {
                            self::apiRequest('cloud/log',['version'=>$v['version'],'status'=>0,'msg'=>$e->getMessage()]);
                            $result = [
                                'code'=>0,
                                'msg'=>$e->getMessage(),
                                'data'=>''
                            ];
                        }
                    }else{
                        $result = [
                            'code'=>406,
                            'msg'=>'本地已经是最新版',
                            'data'=>''
                        ];
                    }
                }
            }
        }
        return $result;
    }

    //手动上传更新包
    public function upgrade($file_path,$version,$build)
    {
        $base_dir = root_path();
        // 本地缓存路径
        $path = runtime_path('update');
        // 没有就创建
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        // 设定缓存目录名称
        $cache_dir = $path.DS;

        //下载成功 解压缩
        $zip_res = $this->dealZip($file_path ,$cache_dir);
        // 判断解压是否成功
        if ($zip_res == 406) {
            $result = [
                'code'=>0,
                'msg'=>'文件解压缩失败',
                'data'=>''
            ];
        } else {
            if(!is_dir($cache_dir.'sql'.DS))
            {
                mkdir($cache_dir.'sql'.DS,0777,true);
                $this->fetchSql($cache_dir.'sql'.DS);
            }
            // php文件合并 返回处理的文件数
            $file_up_res = $this->copyMerge($cache_dir.'script\\',$base_dir);
            if (empty($file_up_res)) {
                $result = [
                    'code'=>0,
                    'msg'=>'文件移动合并失败',
                    'data'=>''
                ];
            }else{
                // 更新完改写网站本地版号
                $string = "<?php\r\n return ".var_export(['version'=>$version,'build'=>$build], true).';';
                $write_res = file_put_contents(config_path().'version.php',$string);
                if (empty($write_res)) {
                    $result = [
                        'code'=>0,
                        'msg'=>'本地更新日志改写失败',
                        'data'=>''
                    ];
                }else{
                    // 删除临时文件
                    $del_res = $this->delDir($cache_dir);
                    if (empty($del_res)) {
                        $result = [
                            'code'=>0,
                            'msg'=>'更新缓存文件删除失败',
                            'data'=>''
                        ];
                    }else{
                        $result = [
                            'code'=>1,
                            'msg'=>'本地升级已完成',
                            'data'=>''
                        ];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 版本号检查
     * @param $version
     * @return mixed
     */
    public static function checkVersionText($version)
    {
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,2}\.[0-9]{1,2}$/', $version);
    }

    /**
     * 字符版本转数字
     * @param $version
     * @return false|int
     */
    public static function versionToInteger($version)
    {
        if(self::checkVersionText($version))
        {
            list($major, $minor, $sub) = explode('.', $version);
            $integer_version = $major*10000 + $minor*100 + $sub;
            return intval($integer_version);
        }
        return false;
    }

    /**
     * 版本比较
     * @param $version1
     * @param $version2
     * @return int|void
     */
    public static function compareVersion($version1, $version2)
    {
        if (self::checkVersionText($version1) && self::checkVersionText($version2))
        {
            $version1_code = self::versionToInteger($version1);
            $version2_code = self::versionToInteger($version2);
            if($version1_code>$version2_code) {
                return 1;
            }else{
                return 0;
            }
        }
    }

    /**
     * 整数版本号转字符串　例如 1000000=001000000=001.000.000=1.0.0
     * @param int $ver
     * @return string
     */
    public static function versionToString(int $ver): string
    {
        $ver = $ver . "";
        $v3 = (int) substr($ver, -1);
        $v2 = (int) substr($ver, -2, 1);
        $v1 = (int) substr($ver, 0, strlen($ver) - 2);

        return "{$v1}.{$v2}.{$v3}";
    }
}