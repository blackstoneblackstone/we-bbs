<?php
namespace app\backend\content;

use app\common\controller\Backend;
use think\exception\ValidateException;
use think\facade\Request;

class Feature extends Backend
{
    protected $table = 'feature';

    public function index()
    {
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db($this->table)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addText('id','ID','','','','true')
            ->addImage('image','专题封面')
            ->addLink('title','专题标题',get_url('feature/detail',['token'=>'__url_token__']))
            ->addText('description','专题描述','','','','true')
            ->addStatus('status','状态',1)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            // 字段规则验证
            try {
                validate(\app\validate\Feature::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if(db($this->table)->where(['url_token'=>$data['url_token']])->value('id'))
            {
                $this->error('专题标识已存在');
            }

            $topics = $data['topics'];
            unset($data['topics']);
            $data['topic_count'] = count($topics);
            $feature_id = db($this->table)->insertGetId($data);
            if ($feature_id) {
                if($topics)
                {
                    foreach ($topics as $k=>$v)
                    {
                        db('feature_topic')->insert([
                            'feature_id'=>$feature_id,
                            'topic_id'=>$v
                        ]);
                    }
                }

                $this->success('添加成功','index');
            }
            $this->error('添加失败');
        }
        return $this->formBuilder
            ->addText('title','专题标题','填写专题标题')
            ->addText('url_token','专题别名','填写专题别名')
            ->addImage('image','专题封面','','','','','','','feature')
            ->addTextarea('description','专题描述','请输入专题描述')
            ->addSelect2('topics','专题话题','选择专题话题','','','','','','',(string)url('choose'),1)
            ->addText('seo_title','SEO标题','填写SEO标题')
            ->addTag('seo_keywords','SEO关键词','请输入SEO关键词,默认选择话题作为关键词','','','','')
            ->addTextarea('seo_description','SEO描述','请输入SEO描述')
            ->addRadio('status','启用状态','',['1' => '启用','0' => '禁用'],'1')
            ->fetch();
    }

    public function edit()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            // 字段规则验证
            try {
                validate(\app\validate\Feature::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if(db($this->table)->where(['url_token'=>$data['url_token']])->where('id','<>',$data['id'])->value('id'))
            {
                $this->error('专题标识已存在');
            }

            $topics = $data['topics'];
            unset($data['topics']);
            $data['topic_count'] = count($topics);
            if (db($this->table)->update($data)) {
                if($topics)
                {
                    db('feature_topic')->where('feature_id',$data['id'])->delete();
                    foreach ($topics as $k=>$v)
                    {
                        db('feature_topic')->insert([
                            'feature_id'=>$data['id'],
                            'topic_id'=>$v
                        ]);
                    }
                }

                $this->success('更新成功','index');
            }
            $this->error('更新失败');
        }

        $info = db($this->table)->find(input('id','0','intval'));
        $info['topics'] = db('feature_topic')
            ->where(['feature_id'=>$info['id']])
            ->column('topic_id');
        $info['topics'] = implode(',',$info['topics']);

        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addText('title','专题标题','填写专题标题')
            ->addText('url_token','专题别名','填写专题别名')
            ->addImage('image','专题封面','','','','','','','feature')
            ->addTextarea('description','专题描述','请输入专题描述')
            ->addSelect2('topics','专题话题','选择专题话题','',$info['topics'],'','','','',(string)url('choose'),1)
            ->addText('seo_title','SEO标题','填写SEO标题')
            ->addTag('seo_keywords','SEO关键词','请输入SEO关键词,默认选择话题作为关键词','','','','')
            ->addTextarea('seo_description','SEO描述','请输入SEO描述')
            ->addRadio('status','启用状态','',['1' => '启用','0' => '禁用'],'1')
            ->fetch();
    }

    public function choose()
    {
        if($this->request->isPost())
        {
            $rows = $this->request->post('rows',get_setting("contents_per_page",15));
            $keyWord = $this->request->post('keyWord');
            $value = $this->request->post('value','');
            $page = $this->request->post('page',1);

            if ($value) {
                $result = db('topic')->whereIn('id',$value)->column('id,title as text');
                $this->result($result);
            }

            $where=[];
            if ($keyWord) {
                $where[]=['title','like','%'.$keyWord.'%'];
            }
            $where[] = ['status','=',1];

            $result = db('topic')
                ->where($where)
                ->field('id,title as text')
                ->paginate(
                    [
                        'list_rows' => $rows,
                        'page' => $page,
                        'query' => request()->param(),
                    ]
                );
            return json($result);
        }
    }
}