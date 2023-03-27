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

namespace app\backend\content;
use app\common\controller\Backend;
use app\common\library\helper\ExcelHelper;
use think\facade\Request;
use app\model\Article as ArticleModel;

class Article extends Backend
{
    protected $table = 'article';

    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['title', '文章标题','link',get_url('article/detail',['id'=>'__id__'])],
            ['comment_count','评论数量','number','','','',true],
            ['view_count','浏览数量','number','','','',true],
            ['user_name','用户','link',get_url('people/index',['name'=>'__url_token__'])],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['text', 'title', '文章标题', 'LIKE'],
        ];

        $status = $this->request->param('status',1);
        if(!$status)
        {
            $right_button = [
                'recover' => [
                    'title'       => '恢复文章',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-sm aw-ajax-get',
                    'url'        => (string)url('manager', ['id' => '__id__','type'=>'recover']),
                    'target'      => '',
                    'confirm'     =>'是否恢复文章？',
                    'href' => ''
                ],
                'remove' => [
                    'title'       => '彻底删除',
                    'icon'        => '',
                    'class'       => 'btn btn-danger btn-sm aw-ajax-get',
                    'url'        => (string)url('manager', ['id' => '__id__','type'=>'remove']),
                    'target'      => '',
                    'confirm'     =>'是否彻底删除该问题？',
                    'href' => ''
                ],
            ];
            $top_button = [
                'export'=>[
                    'title'   => '导出',
                    'icon'    => 'fa fa-download',
                    'class'   => 'btn btn-warning',
                    'href'    => '',
                    'onclick' => 'AWS_ADMIN.table.export({status:'.$status.'})'
                ],
                'recover'=>[
                    'title'   => '恢复删除',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-success multiple disabled',
                    'href'    => '',
                    'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('manager') .'","恢复删除文章","recover")',
                ],
                'remove'=>[
                    'title'   => '彻底删除',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-danger multiple disabled',
                    'href'    => '',
                    'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('manager') .'","恢复删除文章","remove")',
                ],
            ];
        }else{
            $right_button = [
                'config' => [
                    'title'       => '编辑',
                    'icon'        => 'fa fa-edit',
                    'class'       => 'btn btn-success btn-xs',
                    'href'        => get_url('article/publish', ['id' => '__id__']),
                    'target'      => '_blank',
                ],
                'seo' => [
                    'title'       => 'SEO设置',
                    'icon'        => '',
                    'href'       =>'',
                    'class'       => 'btn btn-danger btn-xs aw-ajax-open',
                    'url'        => (string)url('seo', ['id' => '__id__']),
                ],
            ];
            $top_button = ['delete','export'=>[
                'title'   => '导出',
                'icon'    => 'fa fa-download',
                'class'   => 'btn btn-warning',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.table.export({status:'.$status.'})'
            ]];
        }

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));

            // 排序处理
            return db($this->table)
                ->alias('a')
                ->where($where)
                ->where(['a.status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->join('users u','a.uid=u.uid')
                ->field('a.*,u.user_name,u.url_token')
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons($right_button)
            ->addTopButtons($top_button)
            ->setLinkGroup([
                [
                    'title'=>'列表',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'已删除',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
            ])
            ->fetch();
    }

    // 删除
    public function delete()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);
                if(ArticleModel::removeArticle($ids))
                {
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }else{
                    return ['error' => 1, 'msg' => '删除失败'];
                }
            }
            if(ArticleModel::removeArticle($id))
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return ['error' => 1, 'msg' => '删除失败'];
        }
    }

    public function seo($id=0)
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $result = ArticleModel::update($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        $info =ArticleModel::find($id);

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('seo_title','SEO名称','填写SEO名称',$info['seo_title'])
            ->addText('seo_keywords','SEO关键词','填写SEO关键词',$info['seo_keywords'])
            ->addTextarea('seo_description','SEO描述','填写SEO描述',$info['seo_description'])
            ->fetch();
    }

    /**
     * 通用管理
     */
    public function manager()
    {
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $id = is_array($id) ? $id : explode(',',$id);

        if(empty($id))
        {
            return $this->error('请选择要操作的数据');
        }

        switch ($type)
        {
            //恢复删除
            case 'recover':
                if(ArticleModel::recordArticle($id))
                {
                    return $this->success('恢复成功');
                }
                return $this->error('恢复失败');
                break;

            //彻底删除
            case 'remove':
                if(ArticleModel::removeArticle($id,true))
                {
                    return $this->success('删除成功');
                }
                return $this->error(ArticleModel::getError());
                break;
        }
    }

    //导出数据
    public function export($type='')
    {
        $columns = $this->makeBuilder->getListColumns($this->table);
        $tableInfo = $this->table();
        $search = $this->makeBuilder->getSearchColumn($this->table);
        $where = $this->makeBuilder->getWhere($search);
        $isAsc = $this->request->param('isAsc') ?? 'desc';
        $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
        $status = $this->request->param('status',1);
        if(!$tableInfo['page'] || $type=='all')
        {
            $list = db($this->table)
                ->where($where)
                ->where(['status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->select()
                ->toArray();
        }else{
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));

            $list =  db($this->table)
                ->where($where)
                ->where(['status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray()['data'];
        }
        ExcelHelper::exportData($list,$columns,$tableInfo['title']);
    }
}