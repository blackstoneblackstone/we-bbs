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
use app\common\library\helper\TreeHelper;
use Overtrue\Pinyin\Pinyin;
use app\model\Topic as TopicModel;
use think\exception\ValidateException;
use think\facade\Request;

class Topic extends Backend
{
    protected $table='topic';

    public function initialize()
    {
        parent::initialize();
        $this->model = new TopicModel();
    }

    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['title','标题','link',get_url('topic/detail',['id'=>'__id__'])],
            ['pic','图片','image'],
            ['discuss','讨论'],
            ['discuss_week','周讨论'],
            ['discuss_month','月讨论'],
            ['focus','关注'],
            ['is_parent', '根话题', 'tag', '0',[1 => '是',0 => '否']],
            ['lock', '是否锁定', 'status', '0',[1 => '是',0 => '否']],
            ['top', '是否推荐', 'status', '0',[1 => '是',0 => '否']],
        ];
        $search = [
            ['text', 'title', '话题标题', 'LIKE'],
        ];

        $status = $this->request->param('status',0);

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            if($status)
            {
                $where[]=['is_parent','=',1];
                /*$result = [];
                $list= db($this->table)
                    ->where($where)
                    ->order([$orderByColumn => $isAsc])
                    ->select()
                    ->toArray();
                foreach ($list as $v)
                {
                    $result = array_merge(TopicModel::getTreeTopicList($v['id']),$result);
                }

                foreach ($result as $k => $v) {
                    $result[$k]['title'] = $v['left_title'];
                }

                return [
                    'total' => count($result),
                    'per_page' => 10000,
                    'current_page' => 1,
                    'last_page' => 1,
                    'data' => $result,
                ];*/
            }

            // 排序处理
            return db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete','add_parent'=>[
                'title'   => '新增根话题',
                'icon'    => 'fa fa-plus',
                'class'   => 'btn btn-warning aw-ajax-open',
                'href'    => '',
                'url' => (string)url('add',['is_parent'=>1])
            ]])
            /*->setPagination($status?'false':'true')
            ->setParentIdField('pid')*/
            ->setLinkGroup([
                [
                    'title'=>'全部',
                    'link'=>(string)url('index'),
                    'active'=> !$status
                ],
                [
                    'title'=>'根话题',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
            ])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');

            // 字段规则验证
            try {
                validate(\app\validate\Topic::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if(db('topic')->where(['title'=>$data['title']])->value('id'))
            {
                $this->error('话题标题已存在');
            }
            $pinyin = new Pinyin();
            $data['url_token'] = $pinyin->permalink($data['title'],'');

            if(isset($data['is_parent']) && $data['is_parent'])
            {
                $data['pid'] = 0;
            }

            if(db('topic')->where(['url_token'=>$data['url_token']])->value('id'))
            {
                $data['url_token'] = $data['url_token'].time();
            }
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        $list = TopicModel::getTreeTopicListHtml();
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }

        $build = $this->formBuilder;
        if($is_parent = $this->request->get('is_parent'))
        {
            $build->addHidden('is_parent',$is_parent);
        }else{
            $build->addSelect('pid','父级话题','选择父级话题',$result);
        }
        return $build->addImage('pic','话题封面','上传话题封面')
            ->addText('title','话题名称','填写话题名称')
            ->addEditor('description','话题描述')
            ->addText('seo_title','SEO标题','填写SEO标题')
            ->addText('seo_keywords','SEO关键词','填写SEO关键词')
            ->addText('seo_description','SEO描述','填写SEO描述')
            ->addRadio('lock','是否锁定','选择是否锁定',['0' => '未锁定','1' => '已锁定'],'0')
            ->addRadio('top','是否推荐','选择是否推荐',['0' => '不推荐','1' => '已推荐'],'0')
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');

            // 字段规则验证
            try {
                validate(\app\validate\Topic::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            $pinyin = new Pinyin();
            $data['url_token'] = $pinyin->permalink($data['title'],'');

            /*if($data['is_parent'])
            {
                $data['pid'] = 0;
            }*/

            if(db('topic')->where(['url_token'=>$data['url_token']])->value('id'))
            {
                $data['url_token'] = $data['url_token'].time();
            }

            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            }
            $this->error('提交失败或数据无变化');

        }

        $info =$this->model->find($id)->toArray();

        $list = db('topic')->where(['is_parent'=>1])->column('pid,id,title');

        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addRadio('is_parent','作为根话题','选择是否作为根话题',['1' => '是','0' => '否'],$info['is_parent'])
            ->addSelect('pid','父级话题','选择根话题',$result,$info['pid'])
            /*->setRadioTrigger('is_parent',1,'pid','hide','hide')*/
            ->setRadioTrigger('is_parent',0,'pid')
            ->addImage('pic','话题封面','上传话题封面',$info['pic'])
            ->addText('title','话题名称','填写话题名称',$info['title'])
            ->addEditor('description','话题描述','',htmlspecialchars_decode($info['description']))
            ->addText('seo_title','SEO标题','填写SEO标题',$info['seo_title'])
            ->addText('seo_keywords','SEO关键词','填写SEO关键词',$info['seo_keywords'])
            ->addText('seo_description','SEO描述','填写SEO描述',$info['seo_description'])
            ->addRadio('lock','是否锁定','选择是否锁定',['0' => '未锁定','1' => '已锁定'],$info['lock'])
            ->addRadio('top','是否推荐','选择是否推荐',['0' => '不推荐','1' => '已推荐'],$info['top'])
            ->fetch();
    }
}