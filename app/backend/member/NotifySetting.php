<?php
namespace app\backend\member;
use app\common\controller\Backend;
use think\facade\Request;

class NotifySetting extends Backend
{
    protected $table = 'users_notify_setting';
    public function index($group='')
    {
        $columns = [
            ['title', '通知标题'],
            ['name','通知标识'],
            ['subject','通知主题'],
            ['user_setting','允许配置','tag','',[0=>'不允许',1=>'允许']],
            ['type','通知类型'],
            ['group','通知分组','tag','',get_dict('notify_group')],
            ['status', '是否启用','status','',[0=>'禁用',1=>'启用']],
        ];

        if ($this->request->param('_list')) {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            $where = [];
            if($group = $this->request->param('group'))
            {
                $where = ['group'=>$group];
            }
            // 排序处理
            return db('users_notify_setting')
                ->order([$orderByColumn => $isAsc])
                ->where($where)
                ->paginate([
                    'query' => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        $groups[] = [
            'title'=>'全部',
            'link'=>(string)url('index'),
            'active'=> $group ? 0 : 1
        ];
        foreach (get_dict('notify_group') as $key=>$value)
        {
            $groups[]=[
                'title'=>$value,
                'link'=>(string)url('index?group='.$key),
                'active'=> $group==$key ? 1 : 0
            ];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->setDataUrl((string)url('index?_list=1&group='.$group))
            ->addColumns($columns)
            ->addTopButtons(['add'])
            ->addRightButtons(['edit'])
            ->addColumn('right_button', '操作', 'btn')
            ->setLinkGroup($groups)
            ->fetch();
    }

    public function add()
    {
        if ($this->request->isPost())
        {
            $data = $this->request->except(['file'], 'post');
            if ($data) {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        $data[$k] = implode(',', $v);
                    }
                }
            }
            $result = db('users_notify_setting')->insert($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }

        $type = json_decode(db('config')->where(['name'=>'notify_type'])->value('option'),true);
        return $this->formBuilder
            ->addSelect('group','通知分组','选择通知分组,通知分组名称可在”系统管理->系统配置->字典管理”中进行配置',get_dict('notify_group'))
            ->addText('name','通知标识','通知标识')
            ->addText('title','通知标题','通知标题')
            ->addCheckbox('type','通知类型','选择通知类型',$type,'site,email')
            ->addTextarea('subject','通知主题','<blockquote>通知主题,可用变量:[#site_name#] 网站名称,[#title#]内容标题,[#time#]通知时间,[#user_name#]被通知人昵称,[#from_username#]来源用户</blockquote>')
            ->addTextarea('message','通知详情','<blockquote>通知详情,可用变量:[#site_name#] 网站名称,[#subject#] 通知主题,[#title#]内容标题,[#time#]通知时间,[#user_name#]被通知人昵称,[#from_username#]来源用户</blockquote>')
            ->addRadio('user_setting','允许用户设置','选择是否允许用户设置',['1' => '是','0' => '否'],0)
            ->addRadio('status','启用状态','选择启用状态',['0' => '禁用','1' => '启用'],1)
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            if ($data) {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        $data[$k] = implode(',', $v);
                    }
                }
            }
            $result = db($this->table)->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }
        $info = db($this->table)->where($this->getPk(),$id)->find();
        $readonly =  $info['system'] ? 'readonly' : '';
        $type = json_decode(db('config')->where(['name'=>'notify_type'])->value('option'),true);
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('group','通知分组','选择通知分组,通知分组名称可在”系统管理->系统配置->字典管理“中进行配置',get_dict('notify_group'),$info['group'])
            ->addText('name','通知标识','通知标识,系统内置标识不允许修改',$info['name'],$readonly)
            ->addText('title','通知标题','通知标题',$info['title'])
            ->addCheckbox('type','通知类型','选择通知类型',$type,$info['type'])
            ->addTextarea('subject','通知主题','<blockquote>通知主题,可用变量:[#site_name#] 网站名称,[#title#]内容标题,[#time#]通知时间,[#user_name#]被通知人昵称,[#from_username#]来源用户</blockquote>',$info['subject'])
            ->addTextarea('message','通知详情','<blockquote>通知详情,可用变量:[#site_name#] 网站名称,[#subject#] 通知主题,[#title#]内容标题,[#time#]通知时间,[#user_name#]被通知人昵称,[#from_username#]来源用户</blockquote>',$info['message'])
            ->addRadio('user_setting','允许用户设置','选择是否允许用户设置',['1' => '是','0' => '否'],$info['user_setting'])
            ->addRadio('status','启用状态','选择启用状态',['0' => '禁用','1' => '启用'],$info['status'])
            ->fetch();
    }
}