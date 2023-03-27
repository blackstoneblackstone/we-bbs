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

namespace app\backend\wechat;
use app\model\wechat\WechatAccount;
use app\common\controller\Backend;
use think\App;
use think\facade\Request;

class Account extends Backend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->table = 'wechat_account';
        $this->model = new WechatAccount();
    }

    /**
     * 公众号列表
     */
    public function index()
    {
        $columns = [
            ['id', '编号'],
            ['name', '公众号名称'],
            ['qrcode', '二维码', 'image'],
            ['logo', 'logo', 'image'],
            ['type', '公众号类型','tag',1,[
                1=>'普通订阅号',
                2=>'认证订阅号',
                3=>'普通服务号',
                4=>'认证服务号/认证媒体/政府订阅号'
            ]],
            ['related', '接入地址'],
            ['status', '接入状态', 'status', '0', [
                ['0' => '禁用'],
                ['1' => '启用']
            ]]
        ];

        $search = [];
        if ($this->request->param('_list') == 1) {
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            return db('wechat_account')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$this->request->param('pageSize',get_setting("contents_per_page",15))
                ])
                ->toArray();
        }

        // 构建页面
        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add'])
            ->fetch();
    }

    /**
     * 添加公众号
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if($data['status'])
            {
                WechatAccount::update(['status'=>0],['status'=>1]);
            }

            if ($res = WechatAccount::create($data))
            {
                $related = (string)get_url('wechat/index',['id'=>$res['id']],'',true);
                WechatAccount::update(['related'=>$related],['id'=>$res['id']]);
               return $this->success('添加成功', url('index'));
            }
            $this->error('添加失败');
        }

        //构建表单
        return $this->formBuilder
            ->addText('name', '公众号名称', '在4到25个字符之间')
            ->addRadio('type', '公众号类型', '', [
                1 => '普通订阅号',
                2 => '认证订阅号',
                3 => '普通服务号',
                4 => '认证服务号/认证媒体/政府订阅号'
            ], 1)
            ->addText('app_id', 'APP_ID', '填写公众号APP_ID')
            ->addText('app_secret', 'APP_SECRET', '填写公众号APP_SECRET')
            ->addText('token', 'Token', '填写公众号Token')
            ->addText('origin_id', '原始ID', '填写公众号原始ID')
            ->addText('aes_key', 'EncodingAESKey', 'EncodingAESKey，兼容与安全模式下请一定要填写！')
            ->addImage('qrcode', '二维码')
            ->addImage('logo', 'LOGO')
            ->addRadio('status', '接入状态', '', [
                1 => '启用',
                0 => '关闭',
            ], 1)
            ->fetch();
    }

    /**
     * 编辑公众号
     */
    public function edit($id=0)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if($data['status'])
            {
                WechatAccount::update(['status'=>0],['status'=>1]);
            }
            $res = WechatAccount::update($data);
            if ($res) {
                $this->success('添加成功', url('index'));
            } else {
                $this->error('添加失败');
            }
        }
        $info = db($this->table)->find($id);
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name', '公众号名称', '在4到25个字符之间',$info['name'])
            ->addRadio('type', '公众号类型', '', [
                1 => '普通订阅号',
                2 => '认证订阅号',
                3 => '普通服务号',
                4 => '认证服务号/认证媒体/政府订阅号'
            ], $info['type'])
            ->addText('app_id', 'APP_ID', '填写公众号APP_ID',$info['app_id'])
            ->addText('app_secret', 'APP_SECRET', '填写公众号APP_SECRET',$info['app_secret'])
            ->addText('token', 'Token', '填写公众号Token',$info['token'])
            ->addText('origin_id', '原始ID', '填写公众号原始ID',$info['origin_id'])
            ->addText('aes_key', 'EncodingAESKey', 'EncodingAESKey，兼容与安全模式下请一定要填写！',$info['aes_key'])
            ->addImage('qrcode', '二维码','',$info['qrcode'])
            ->addImage('logo', 'LOGO','',$info['logo'])
            ->addRadio('status', '接入状态', '', [
                1 => '启用',
                0 => '关闭',
            ],$info['status'])
            ->fetch();
    }
}