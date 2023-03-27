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

namespace app\frontend;
use app\common\controller\Base;
use app\common\library\helper\FileHelper;
use app\common\library\helper\LocalUpgradeHelper;
use app\common\library\helper\UpgradeHelper;
use think\App;
use think\facade\Db;

class Upgrade extends Base
{
    public $versionList;
    public $db_version = 0;
    public $build_version = 0;
    public $ignore_sql;
    
    public function __construct(App $app)
    {
        parent::__construct($app);
        /*if(get_setting('local_upgrade_enable')!='Y')
        {
            $this->redirect(get_url('index/index'));
        }*/

        $this->view->config([
            'view_path' => root_path('update'). 'view' . DS,
        ]);
        @set_time_limit(0);

        $this->db_version = get_setting('db_version','403');

        if ($this->db_version < 403)
        {
            $this->error('当前升级器只支持 4.1.0 及以上版本升级, 你当前版本太低');
        }

        $this->versionList = FileHelper::getList(root_path('update').'upgrade');
        if (!in_array($this->db_version, $this->versionList))
        {
            $this->db_version = $this->db_version - 1;
            $this->ignore_sql = true;
        }

        if (in_array($this->db_version, $this->versionList) AND ($this->ignore_sql || $this->db_version==403))
        {
            $this->db_version = $this->db_version + 1;
        }
        else if (!in_array($this->db_version, $this->versionList) AND $this->request->action() != 'final' AND $this->request->action() != 'script')
        {
            if ($this->db_version > end($this->versionList))
            {
                $this->success('您的程序已经是最新版本');
            }
            else
            {
                $this->error(L('无法定位您的程序版本, 请手动执行升级, Build: %s', $this->db_version));
            }
        }
    }

    public function index()
    {
        $this->assign('db_version', $this->db_version);
        $this->assign('versions', end($this->versionList));
        return $this->fetch('/index');
    }

    public function run()
    {
        $key = array_search($this->db_version,$this->versionList);
        $db = array_slice($this->versionList, $key+1);
        $error = '';
        //$res = [];
        foreach ($db as $k => $v) {
            if ($this->db_version <= $v)
            {
                $res = LocalUpgradeHelper::upgrade(root_path('update').'upgrade'.DS.$v,$v,$this->build_version);
                if(!$res['code'])
                {
                    $error = $res['msg'];
                    break;
                }
            }
        }
        if(!$error)
        {
            $this->success('升级完成, 您的程序已经是最新版本',get_url('index/index'));
        }
        $this->error($error);
    }
}