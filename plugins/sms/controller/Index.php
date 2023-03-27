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
namespace plugins\sms\controller;
use app\common\controller\Backend;
use think\facade\Request;

/**
 * 短信记录
 * Class Index
 * @package plugins\sms\controller
 */
class Index extends Backend
{
    protected $table = 'sms_log';
    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['mobile', '手机号'],
            ['send_type','短信运营商'],
            ['template_code','模板ID'],
            ['content','短信内容'],
            ['ip','IP地址'],
            ['create_time', '发送时间','datetime'],
        ];
        $search = [
            ['select', 'send_type', '短信运营商', '','=',[
                '阿里云短信',
                '腾讯云短信'
            ]],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            // 排序处理
            return db('sms_log')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$this->request->param('pageSize',get_setting("contents_per_page",15))
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['delete'])
            ->addTopButtons(['delete'])
            ->fetch();
    }
}