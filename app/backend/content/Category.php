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
use think\exception\ValidateException;

class Category extends Backend
{
    protected $table = 'category';

    public function index()
    {
        $category = ['common'=>'通用','question'=>'问题','article'=>'文章'];
        $category = array_merge($category,config('aws.category'));
        $type = $this->request->param('type','');
        $links[] = [
            'title'=>'全部',
            'link'=>(string)url('index'),
            'active'=> !$type
        ];
        foreach ($category as $key=>$v){
            $links[] = [
                'title'=>$v,
                'link'=>(string)url('index',['type'=>$key]),
                'active'=> $type==$key?1:0
            ];
        }
        $columns = [
            ['id'  , 'ID'],
            ['icon','分类图标','image'],
            ['title','分类名称'],
            ['type', '分类类型','tag','',$category],
            ['url_token','分类别名'],
            ['status', '是否启用', 'status', '1',['1' => '否','0' => '是']],
        ];

        $search = [
            ['text', 'title', '分类名称', 'LIKE'],
            ['select', 'type', '分类类型', '=',$type,$category]
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            // 排序处理
            $list = db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->select()
                ->toArray();

            $list = TreeHelper::tree($list);
            foreach ($list as $k => $v) {
                $list[$k]['title'] = $v['left_title'];
            }
            return [
                'total' => count($list),
                'per_page' => 10000,
                'current_page' => 1,
                'last_page' => 1,
                'data' => $list,
            ];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->setPagination('false')
            ->setParentIdField('pid')
            ->setLinkGroup($links)
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            // 字段规则验证
            try {
                validate(\app\validate\Category::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            $pinyin = new Pinyin();
            $data['url_token'] = $pinyin->permalink($data['title'],'');
            // 分类类型
            if ($data['pid']) {
                $data['type'] = db($this->table)->where(['id' => $data['pid']])->value('type');
            } else {
                $data['type'] = $data['type'] ?: 'common';
            }
            $result = db($this->table)->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        $list = db('category')
            ->select()
            ->toArray();

        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }
        $category = ['common'=>'通用','question'=>'问题','article'=>'文章'];
        $category = array_merge($category,config('aws.category'));
        return $this->formBuilder
            ->addSelect('pid','父级分类','选择父级分类',$result)
            ->addText('title','分类名称','填写分类名称')
            ->addImage('icon','分类图标','分类导航图标')
            ->addSelect('type','分类类型','选择分类类型',$category)
            ->addTextarea('description','分类描述','填写分类描述')
            ->addText('sort','排序值','填写排序值',0)
            ->setSelectTrigger('pid','','type')
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit( $id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');

            // 字段规则验证
            try {
                validate(\app\validate\Category::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            $pinyin = new Pinyin();
            $data['url_token'] = $pinyin->permalink($data['title'],'');

            if ($data['pid']) {
                $data['type'] = db($this->table)->where(['id' => $data['pid']])->value('type');
            } else {
                $data['type'] = $data['type']?: 'common';
            }

            $result =  db($this->table)->save($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info = db($this->table)->find($id);

        $list = db('category')
            ->select()
            ->toArray();

        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }
        $category = ['common'=>'通用','question'=>'问题','article'=>'文章'];
        $category = array_merge($category,config('aws.category'));
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('pid','父级分类','选择父级分类',$result,$info['pid'])
            ->addText('title','分类名称','填写分类名称',$info['title'])
            ->addImage('icon','分类图标','分类导航图标',$info['icon'])
            ->setSelectTrigger('pid','','type')
            ->addSelect('type','分类类型','选择分类类型',$category,$info['type'])
            ->addTextarea('description','分类描述','填写分类描述',$info['description'])
            ->addText('sort','排序值','填写排序值',$info['sort'])
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }
}