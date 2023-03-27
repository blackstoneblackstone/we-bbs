<?php
namespace app\common\service;
use think\App;
use think\Container;

/**
 * 进程管理服务
 * Class ProcessService
 */
class ProcessService
{

    protected $version='6.06';
    /**
     * 应用实例
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 初始化服务
     */
    public function initialize($code=null)
    {
        return $this;
    }

    /**
     * 静态实例对象
     * @param array $var 实例参数
     * @param boolean $new 创建新实例
     */
    public static function instance(array $var = [], bool $new = false)
    {
        return Container::getInstance()->make(static::class, $var, $new);
    }

    /**
     * Service constructor.
     * @param string $args 指定参数
     * @return string
     */
    public function think(string $args = ''): string
    {
        $root = $this->app->getRootPath();
        return trim("php {$root}think {$args}");
    }

    /**
     * 获取当前应用版本
     * @return string
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * 创建异步进程
     * @param string $command 任务指令
     * @return $this
     */
    public function create(string $command): ProcessService
    {
        if ($this->isWin()) {
            $this->exec(__DIR__ . "/bin/console.exe {$command}");

        } else {
            $this->exec("{$command} > /dev/null 2>&1 &");
        }
        return $this;
    }

    /**
     * 查询相关进程列表
     * @param string $command 任务指令
     * @return array
     */
    public function query(string $command): array
    {
        $list = [];
        if ($this->isWin()) {
            $lines = $this->exec('wmic process where name="php.exe" get processid,CommandLine', true);
            foreach ($lines as $line) {
                if ($this->_isSub($line, $command) !== false) {
                    $attr = explode(' ', $this->_space($line));
                    $list[] = ['pid' => array_pop($attr), 'cmd' => join(' ', $attr)];
                }
            }
        } else {
            $lines = $this->exec("ps ax|grep -v grep|grep \"{$command}\"", true);
            foreach ($lines as $line) {
                if ($this->_isSub($line, $command) !== false) {
                    $attr = explode(' ', $this->_space($line));
                    [$pid] = [array_shift($attr), array_shift($attr), array_shift($attr), array_shift($attr)];
                    $list[] = ['pid' => $pid, 'cmd' => join(' ', $attr)];
                }
            }
        }

        return $list;
    }

    /**
     * 关闭任务进程
     * @param integer $pid 进程号
     * @return boolean
     */
    public function close(int $pid): bool
    {
        if ($this->isWin()) {
            $this->exec("wmic process {$pid} call terminate");
        } else {
            $this->exec("kill -9 {$pid}");
        }
        return true;
    }

    /**
     * 立即执行指令
     * @param string $command 执行指令
     * @param bool $outer 返回类型
     * @return string|array
     */
    public function exec(string $command, bool $outer = false)
    {
        exec($command, $output);
        return $outer ? $output : join("\n", $output);
    }

    /**
     * 判断系统类型
     * @return boolean
     */
    public function isWin(): bool
    {
        return PATH_SEPARATOR === ';';
    }

    /**
     * 消息空白字符过滤
     * @param string $content
     * @return string
     */
    private function _space(string $content): string
    {
        return preg_replace('|\s+|', ' ', strtr(trim($content), '\\', '/'));
    }

    /**
     * 判断是否包含字符串
     * @param string $content
     * @param string $substr
     * @return boolean
     */
    private function _isSub(string $content, string $substr): bool
    {
        return stripos($this->_space($content), $this->_space($substr)) !== false;
    }
}