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

namespace app\backend\plugin;

use think\facade\Request;
use app\common\controller\Backend;

/**
 * 第三方登录管理
 *
 * @icon fa fa-circle-o
 */
class Third extends Backend
{
    protected $model = null;
    protected $table = 'third';
    public function initialize()
    {
        parent::initialize();
        $this->model = new \plugins\third\model\Third();
    }

    /**
     * 查看
     */
    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['nick_name','用户昵称','link',get_url('people/index',['name'=>'__url_token__'])],
            ['platform','绑定平台','tag','',['qq'=>'QQ','wechat'=>'微信','weibo'=>'微博']],
            ['openid','第三方唯一ID'],
            ['open_username','第三方会员昵称'],
            ['login_time', '登录时间','datetime'],
        ];
        $search = [

        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            // 排序处理
            $data= $this->model
                ->with(['user'])
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$this->request->param('pageSize',get_setting("contents_per_page",15))
                ])
                ->toArray();

            foreach ($data['data'] as $key=>$val)
            {
                $data['data'][$key]['nick_name'] = db('users')->where('uid',$val['uid'])->value('nick_name');
                $data['data'][$key]['url_token'] = db('users')->where('uid',$val['uid'])->value('url_token');
            }
            return $data;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['delete'])
            ->fetch();
    }
}
