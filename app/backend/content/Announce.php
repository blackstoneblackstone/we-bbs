<?php

namespace app\backend\content;
use app\common\controller\Backend;
use think\exception\ValidateException;
use think\facade\Request;

class Announce extends Backend
{
    protected $table = 'announce';

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
                ->alias('a')
                ->order(['sort'=>'ASC',$orderByColumn => $isAsc])
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
            ->addText('id','ID','','','','true')
            ->addDialog('title','公告标题',get_url('announce/detail',['id'=>'__id__']))
            ->addText('view_count','浏览数量','','','','true')
            ->addLink('user_name','用户',get_url('people/index',['name'=>'__url_token__']))
            ->addLabel('status','状态',1,[0=>['text'=>'禁用','label'=>'info'],1=>['text'=>'启用','label'=>'success']])
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
                validate(\app\validate\Announce::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $data['set_top_time'] = $data['set_top'] ? time() : 0;
            $data['uid'] = intval($this->user_id);
            $data['create_time'] = time();
            $result = db($this->table)->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }
        return $this->formBuilder
            ->addText('title','公告标题','填写公告标题')
            ->addEditor('message','公告详情','','','announce')
            ->addText('sort','排序值','值越大越靠前')
            ->addRadio('status','启用状态','',['1' => '启用','0' => '禁用'],'1')
            ->addRadio('set_top','是否置顶','选择是否推荐',['0' => '不置顶','1' => '已置顶'],'0')
            ->fetch();
    }

    public function edit($id=0)
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            // 字段规则验证
            try {
                validate(\app\validate\Announce::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            $data['set_top_time'] = $data['set_top'] ? time() : 0;
            $data['update_time'] = time();
            $result = db($this->table)->where('id',intval($id))->update($data);
            if (!$result) {
                $this->error('更新失败');
            } else {
                $this->success('更新成功','index');
            }
        }

        $info = db($this->table)->where('id',intval($id))->find();
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('title','公告标题','填写公告标题',$info['title'])
            ->addEditor('message','公告详情','',htmlspecialchars_decode($info['message']),'announce')
            ->addText('sort','排序值','值越大越靠前',$info['sort'])
            ->addRadio('status','启用状态','',['1' => '启用','0' => '禁用'],$info['status'])
            ->addRadio('set_top','是否置顶','选择是否推荐',['0' => '不置顶','1' => '已置顶'],$info['set_top'])
            ->fetch();
    }
}