<?php
namespace app\backend;
use app\common\controller\Backend;
use app\common\library\helper\FileHelper;
use app\common\library\helper\MailHelper;
use app\common\library\helper\UpgradeHelper;
use app\model\admin\MenuRule;
use think\App;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;
use app\common\logic\common\StatisticLogic;

class Index extends Backend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        $sys_info['os'] = PHP_OS;
        $sys_info['zlib'] = function_exists('gzclose') ? 'YES' : 'NO';//zlib
        $sys_info['safe_mode'] = (boolean)ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off
        $sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl'] = function_exists('curl_init') ? 'YES' : 'NO';
        $sys_info['web_server'] = $this->request->server('SERVER_SOFTWARE');
        $sys_info['php_version'] = phpversion();
        $sys_info['ip'] = getServerIp();
        $sys_info['file_upload'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
        $sys_info['max_ex_time'] = @ini_get("max_execution_time").'s'; //脚本最大执行时间
        $sys_info['domain'] = $this->request->server('HTTP_HOST');
        $sys_info['memory_limit'] = ini_get('memory_limit');
        $sys_info['version'] = config('version.version');
        $mysqlInfo = \think\facade\Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version'] = $mysqlInfo[0]['version'];
        if (function_exists("gd_info"))
        {
            $gd = gd_info();
            $sys_info['gd_info'] = $gd['GD Version'];
        } else
        {
            $sys_info['gd_info'] = "未知";
        }

        // 用户数据

        $users_info = [
            'users_count' => db('users')->where('status', 1)->count(),
            'users_valid_email_count' => db('users')->where('is_valid_email', 1)->count(),
            'column_count' => db('column')->count(),
            'article_count' => db('article')->where('status', 1)->count(),
            'question_count' => db('question')->where('status', 1)->count(),
            'answer_count' => db('answer')->where('status', 1)->count(),
            'no_answer_count' => db('question')->where('answer_count', 0)->count(),
            'best_answer_count' => db('answer')->where('is_best', 1)->count(),
            'topic_count' => db('topic')->where('status', 1)->count(),
            'attach_count' => db('attach')->where('status', 1)->count(),
            'approval_question_count' => db('approval')->where(['status' => 0, 'type' => 'question'])->count(),
            'approval_answer_count' => db('approval')->where(['status' => 0, 'type' => 'answer'])->count(),
        ];

        $this->view->assign('sysInfo',$sys_info);
        $this->view->assign('usersInfo', $users_info);
        return $this->view->fetch();
    }

    //后台统计
    public function statistic()
    {
        if (!$start_time = strtotime($_GET['start_date'] . ' 00:00:01'))
        {
            $start_time = strtotime('-12 months');
        }

        if (!$end_time = strtotime($_GET['end_date'] . ' 23:59:59'))
        {
            $end_time = time();
        }

        $statistic_tag = $_GET['tag'] ? explode(',', $_GET['tag']) : [];
        if (empty($statistic_tag)) exit;
        if (!$month_list = get_month_list($start_time, $end_time, 'y')) exit;
        $data = $labels = $statistic = $data_template = [];
        foreach ($month_list AS $key => $val)
        {
            $labels[] = $val['year'] . '-' . $val['month'];
            $data_template[] = 0;
        }

        foreach ($statistic_tag AS $key => $val)
        {
            switch ($val)
            {
                case 'new_article':
                case 'new_answer':  // 新增答案
                case 'new_question':    // 新增问题
                case 'new_user':    // 新注册用户
                case 'user_valid':  // 新激活用户
                case 'new_topic':   // 新增话题
                case 'new_answer_vote': // 新增答案投票
                case 'new_answer_thanks': // 新增答案感谢
                    $statistic[] = StatisticLogic::singleTagData($val, $start_time, $end_time);
                    break;
            }
        }

        foreach($statistic AS $key => $val)
        {
            $statistic_data = $data_template;
            foreach ($val AS $k => $v)
            {
                $data_key = array_search($v['date'], $labels);
                $statistic_data[$data_key] = $v['count'];
            }
            $data[] = $statistic_data;
        }

        $this->success('', null, ['labels' => $labels, 'data' => $data]);
    }

    //系统登录
    public function login()
    {
        if(session('admin_login_uid'))
        {
            $this->redirect(url('index'));
        }

        if($this->request->isPost())
        {
            $postData = $this->request->post();
            $postData['password'] = authCode($postData['password'],'DECODE',$postData['token']);
            if(!$this->auth->login($postData['username'],$postData['password']))
            {
                $this->error('账号或密码错误');
            }
            $this->success('登录成功',url('index/index'));
        }

        if($login_user_info = session('login_user_info'))
        {
            $this->assign('user_info',$login_user_info);
        }else{
            $this->redirect(get_url('account/login'));
        }

        return $this->fetch();
    }

    //退出登录
    public function logout()
    {
        session('admin_user_info',null);
        session('admin_login_uid',null);
        $this->success('退出成功','login');
    }

    //清除缓存
    public function clear()
    {
        $type = $this->request->param('type','cache');
        $path = runtime_path();
        if($type!='all')
        {
            $path = runtime_path($type);
        }

        if($type=='cache' || $type=='all')
        {
            Cache::clear();
        }

        if (FileHelper::delDir($path)) {
            $this->success('清除成功');
        }
        $this->error('清除失败');
    }

    /**
     * 图标
     * @return mixed
     */
    public function icons()
    {
        return $this->view->fetch('global/icons');
    }

    //发送测试邮件
    public function send_test_email()
    {
        if($this->request->isPost())
        {
            $email = $this->request->post('email');
            $subject = get_setting('site_name').'测试邮件';
            $message = '该邮件为测试邮件，请勿回复';
            $res = MailHelper::sendEmail($email, $subject, $message);
            if ($res['code'] == 0) $this->error($res['message']);
            $this->success('测试邮件发送成功');
        }
        return $this->formBuilder
            ->addText('email','邮箱地址','请输入测试邮箱地址')
            ->fetch();
    }

    //检查缓存启用状态
    public function cache_type_check()
    {
        if($this->request->isPost())
        {
            $cache_type = $this->request->post('cache_type','file');
            $message = '';
            Config::set([
                // 服务器地址
                'host' => $this->request->post('cache_host','127.0.0.1'),
                // 端口号
                'port' => $this->request->post('cache_port','11211'),
                // 密码
                'password'=> $this->request->post('cache_password','11211'),
            ],'aws');
            try {
                Cache::store($cache_type)->set('aws_cache_test', 'WeCenter');
            }catch (\Exception $e){
                $message = $e->getMessage();
            }
            if(!$message)
            {
                $data = $this->request->post();

                foreach ($data as $k => $v) {
                    if (is_array($v) && isset($v['key']) && isset($v['value'])) {
                        $value = [];
                        foreach ($v['key'] as $k1=>$v1)
                        {
                            $value[$v1] = $v['value'][$k1];
                        }
                        $data[$k] = $value;
                    }
                }
                $configList = [];
                foreach (db('config')->select()->toArray() as $v)
                {
                    if (isset($data[$v['name']])) {
                        $value = $data[$v['name']];
                        $option = json_decode($v['option'],true);
                        if(in_array($v['type'],['array','images','files'])){
                            $option = $value;
                            $value = 0;
                        } else{
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        $v['value'] = $value;
                        $v['option'] = json_encode($option,JSON_UNESCAPED_UNICODE);
                        $configList[] = $v;
                    }
                }
                $ConfigModel = new \app\model\Config();
                $ConfigModel->saveAll($configList);
                $this->success('修改成功');
            }

            $this->error($message);
        }
        $memcacheEnable = class_exists('Memcache')?1:0;
        $memcachedEnable = class_exists('Memcached')?1:0;
        $redisEnable = class_exists('Redis')?1:0;

        $html = 'Memcache模块：'.($memcacheEnable?'<span class="text-green">已开启</span>':'<span class="text-danger">已禁用</span>').'<br>';
        $html.='Memcached模块：'.($memcachedEnable?'<span class="text-green">已开启</span>':'<span class="text-danger">已禁用</span>').'<br>';
        $html.='Redis模块：'.($redisEnable?'<span class="text-green">已开启</span>':'<span class="text-danger">已禁用</span>');
        return $this->formBuilder
            ->setPageTips($html,'info')
            ->addRadio('cache_type','缓存方式','',['redis'=>'Redis','memcached'=>'Memcached','memcache'=>'Memcache'],'redis')
            ->addText('cache_host','主机地址','','127.0.0.1')
            ->addText('cache_port','主机端口')
            ->addText('cache_password','主机密码','没有可不填')
            ->setBtnTitle('submit','检查并配置')
            ->fetch();
    }
}