<?php

namespace app\backend\member;

use app\common\controller\Backend;

use app\common\library\helper\IpLocation;

class Forbidden extends Backend
{
    public function ips()
    {
        $columns = [
            ['id', 'ID'],
            ['ip', '封禁ip'],
            ['address', '真实地址'],
            ['time', '封禁时间', 'datetime'],
        ];

        $search = [
            ['text', 'ip', 'ip', '=']
        ];
        $ip = $this->request->param('ip', '', 'trim');
        if ($this->request->param('_list')) {
            $where = [];
            if ($ip) $where[] = ['ip', '=', $ip];
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize', get_setting("contents_per_page",15));
            // 排序处理
            $data = db('forbidden_ip')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => $this->request->get(),
                    'list_rows' => $pageSize,
                ])
                ->toArray();

            if (!empty($data['data'])) {
                $ipTool = new IpLocation();
                foreach ($data['data'] as &$val) {
                    $val['address'] = $ipTool->getLocation($val['ip'])['country'];
                }
            }

            return $data;
        }

        $topButtons = [
            'add_ip' => [
                'title'   => '封禁ip',
                'icon'    => 'fa fa-plus',
                'class'   => 'btn btn-warning aw-ajax-open',
                'href'    => '',
                'url' => (string) url('add_ip')
            ],
            'un_forbidden_ip' => [
                'title'   => '解除封禁',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-success multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'.(string) url('un_forbidden_ip').'","封禁ip", "list")',
            ]
        ];

        $rightButtons = [
            'un_forbidden_ip' => [
                'title'       => '解除封禁',
                'icon'        => 'fa fa-ban',
                'class'       => 'btn btn-warning btn-sm aw-ajax-get',
                'url'        => (string) url('un_forbidden_ip', ['id' => '__id__']),
                'target'      => '',
                'href' => 'javascript:;',
                'confirm' => '确认解除封禁该IP吗？'
            ]
        ];

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->setDataUrl($this->request->baseUrl().'?_list=1&ip='.$ip)
            ->addTopButtons($topButtons)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons($rightButtons)
            ->setLinkGroup([
                [
                    'title' => '已封禁IP',
                    'link' => (string) url('ips'),
                    'active' => true
                ],
            ])->fetch();
    }

    // 封禁ip
    public function add_ip()
    {
        if ($this->request->isPost()) {
            if (!$ip = $this->request->post('ip', '', 'trim')) $this->error('请填写要封禁的ip');
            $ip = array_unique(explode(',', $ip));
            $time = time();
            $insertData = [];
            foreach ($ip as $key => $val) {
                if ($val && preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $val)) $insertData[] = ['uid' => 0, 'ip' => $val, 'time' => $time];
            }
            if (empty($insertData)) $this->error('都是无效的ip地址');

            if (db('forbidden_ip')->insertAll($insertData)) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }

        return $this->formBuilder
            ->addTextarea('ip','用户ip','填写要封禁的ip,多个ip用英文逗号间隔')
            ->fetch();
    }

    // 解除封禁ip
    public function un_forbidden_ip()
    {
        $id = $this->request->param('id', 0);
        $type = $this->request->param('type', '');

        // 批量解除
        if ('list' == $type) {
            $uid = [];
            $id = explode(',', $id);
            $ips = db('forbidden_ip')->whereIn('id', $id)->select()->toArray();
            foreach ($ips as $ip) {
                if ($ip['uid']) $uid[] = $ip['uid'];
            }

            db('forbidden_ip')->whereIn('id', $id)->delete();
            if (!empty($uid)) db('users')->whereIn('uid', array_unique($uid))->update(['forbidden_ip' => 0]);
            $this->success('操作成功');
        }

        // 单条解除
        $ip = db('forbidden_ip')->where('id', $id)->find();
        db('forbidden_ip')->where('id', $ip['id'])->delete();
        if ($ip['uid']) db('users')->where('uid', $ip['uid'])->update(['forbidden_ip' => 0]);

        $this->success('操作成功');
    }
}
