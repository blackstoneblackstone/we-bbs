<?php
namespace app\backend\extend;
use app\common\controller\Backend;
use think\facade\Request;

class Log extends Backend
{
    protected $table='admin_log';

    public function index()
    {
        $columns = [
            ['id','编号'],
            ['title', '操作标题'],
            ['url', '操作地址'],
            ['url_token','执行用户','link',get_url('people/index',['name'=>'__url_token__'])],
            ['ip','执行者ip'],
            ['create_time', '创建时间','datetime'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            return db('admin_log')
                ->alias('a')
                ->order([$orderByColumn => $isAsc])
                ->join('users u','a.uid=u.uid')
                ->field('a.*,u.user_name,u.url_token')
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->setPageTips('说明：<br>需要记录后台操作日志，可在“系统管理->系统配置->功能配置“中开启记录管理员日志！去<a href="'.(string)url('admin.Config/config',['group'=>7]).'" target="_blank" style="text-decoration: none;">开启>></a>')
            ->addColumns($columns)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['delete'])
            ->addTopButtons(['delete'])
            ->fetch();
    }
}