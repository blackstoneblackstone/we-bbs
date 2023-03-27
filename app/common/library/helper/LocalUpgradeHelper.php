<?php
namespace app\common\library\helper;

class LocalUpgradeHelper
{
    public static function upgrade($path,$version): array
    {
        $base_dir = root_path();
        $cache_dir = $path.DS;
        $sqlRes = self::fetchSql($cache_dir.'sql'.DS);
        if(!$sqlRes['code'])
        {
            return $sqlRes;
        }

        if(is_dir($cache_dir.'program'.DS) && file_exists($cache_dir.'program'.DS.'program.php'))
        {
            require $cache_dir.'program'.DS.'program.php';
        }

        // php文件合并 返回处理的文件数
        $file_up_res = is_dir($cache_dir.'script'.DS) ? self::copyMerge($cache_dir.'script\\',$base_dir,root_path('update').'backup'.DS.config('version.version').DS):[];
        if (empty($file_up_res) && is_dir($cache_dir.'script'.DS) && !empty(FileHelper::getFileList($cache_dir.'script'.DS))) {
            return [
                'code'=>0,
                'msg'=>'文件移动合并失败',
                'data'=>''
            ];
        }else{
            // 更新完改写网站本地版号
            db('config')->where(['name'=>'db_version'])->update([
                'value'=>$version
            ]);
            $version = UpgradeHelper::versionToString($version);
            $build = date('Ymd');
            $string = "<?php\r\n return ".var_export(['version'=>$version,'build'=>$build], true).';';
            $write_res = file_put_contents(config_path().'version.php',$string);
            if (empty($write_res)) {
                return [
                    'code'=>0,
                    'msg'=>'本地更新日志改写失败',
                    'data'=>''
                ];
            }
        }

        return [
            'code'=>1,
            'msg'=>'本地升级已完成',
            'data'=>''
        ];
    }

    public static function fetchSql(string $dir): array
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
                        db()->execute($tempLine);
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

    public static function copyMerge(string $source, string $target,$backup_dir='') {
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
                $count += self::copyMerge( $filename, $target . basename ( $filename ),$backup_dir. basename ( $filename ));
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
}