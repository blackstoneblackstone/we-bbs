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

namespace app\model;

class Score extends BaseModel
{
	protected $name = 'integral_rule';

	//获取积分明细列表
	public static function getScoreList($where=[],$page=1,$per_page=10,$pjax=''): array
    {
		$list = db('integral_log')->where($where)->order('create_time','DESC')->paginate(
			[
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param(),
                'pjax'=>$pjax
			]
		);
		$pageVar = $list->render();
		$list = $list->all();

		foreach ($list as $key=>$val)
		{
			switch ($val['action_type'])
			{
				case 'LOGIN':
					$list[$key]['extend'] = '';
				break;
			}
		}
		return ['list'=>$list,'page'=>$pageVar];
	}
}