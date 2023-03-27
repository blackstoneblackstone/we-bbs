<?php

namespace app\backend\content;
use app\common\controller\Backend;
use app\common\library\helper\TreeHelper;
use think\App;
use think\facade\Request;

class Answer extends Backend
{
    protected $table = 'answer';

    public function index($question_id=0)
    {
        $columns = [
            ['id'  , 'ID'],
            ['title', '问题','link',get_url('question/detail',['id'=>'__question_id__','answer'=>'__id__'])],
            ['nick_name', '作者','link',get_url('people/index',['name'=>'__url_token__'])],
            ['content','内容','text'],
            ['against_count','反对数','number'],
            ['agree_count','赞同数','number'],
            ['comment_count','评论数','number'],
            ['is_best', '最佳','bool',0],
        ];
        $search = [];
        $status = $this->request->param('status',1);
        $top_button = $status ? ['delete'] :[
            'remove'=>[
                'title'   => '彻底删除',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-danger multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('delete') .'","彻底删除回答","1")',
            ],
        ];
        $right_button = $status ? ['edit','delete'] : ['real'=>[
            'title'   => '彻底删除',
            'icon'  => 'far fa-trash-alt',
            'class' => 'btn btn-danger btn-sm confirm aw-ajax-get',
            'url'        => (string)url('delete', ['id' => '__id__','type'=>1]),
            'target'      => '',
            'href' => '',
            'confirm'     =>'是否彻底删除该回答？',
        ]];
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));

            // 排序处理
            $data =  db($this->table)
                ->alias('a')
                ->order([$orderByColumn => $isAsc])
                ->where(['a.status'=>$status])
                ->join('users u','a.uid=u.uid')
                ->join('question q','a.question_id=q.id')
                ->field('a.*,u.nick_name,u.url_token,q.title')
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();

            foreach ($data['data'] as $k=>$v)
            {
                $data['data'][$k]['content'] = str_cut(strip_tags(htmlspecialchars_decode($v['content'])),0,100);
            }

            return $data;
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

    //回答详情
    public function edit($id=0)
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            unset($data['question_id']);
            $result = db($this->table)->where('id',intval($id))->update($data);
            if (!$result) {
                $this->error('更新失败或更新内容无变化');
            } else {
                $this->success('更新成功','index');
            }
        }

        $info = db($this->table)->where('id',intval($id))->find();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('question_id','问题标题','',db('question')->where('id',intval($info['question_id']))->value('title'),'readonly disabled')
            ->addEditor('content','回答详情','',htmlspecialchars_decode($info['content']))
            ->fetch();
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $real = $this->request->param('type',0);
        $real = $real==1;
        if (strpos($id, ',') !== false)
        {
            $ids = explode(',',$id);
            if(\app\model\Answer::deleteAnswer($ids,$real)){
                return $this->success('删除成功');
            }else{
                return $this->error('删除失败');
            }
        }

        if(\app\model\Answer::deleteAnswer($id,$real))
        {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}