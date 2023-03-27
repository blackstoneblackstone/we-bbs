<?php
namespace app\model;
use think\facade\Request;

class BrowseRecords extends BaseModel
{
    /**
     * 记录用户浏览记录
     * @param $uid
     * @param $item_id
     * @param $item_type
     * @return bool
     */
    public static function recordViewLog($uid,$item_id,$item_type): bool
    {
        if(!$uid || !$item_id || !$item_type) return false;

        $cache_key = md5('cache_record_view_log_'.$item_type.'_'.$item_id.'_'.$uid);
        $cache_result = cache($cache_key);
        if($cache_result) {
            return true;
        }
        cache($cache_key,$cache_key,['expire'=>60]);
        db('browse_records')->where(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$uid])->update(['status'=>0]);
        return db('browse_records')->insert([
            'item_id'=>$item_id,
            'item_type'=>$item_type,
            'uid'=>$uid,
            'status'=>1,
            'create_time'=>time()
        ]);
    }

    /**
     * 获取用户浏览记录数据
     */
    public static function getRecordViewList($uid,$page=1,$per_page=10,$pjax='wrapMain')
    {
        if(!$uid) return false;

        $data = db('browse_records')
            ->where(['uid'=>$uid,'status'=>1])
            ->order('id','DESC')
            ->paginate([
                'query'     => Request::get(),
                'list_rows' =>$per_page,
                'page'=>$page,
                'pjax'=>$pjax
            ]);

        $pageVar = $data->render();
        $data = $data->toArray();
        $list = $data['data'];

        $result = [];

        if($list)
        {
            foreach ($list as $k=>$v)
            {
                $result[$k] = $v;
                if($v['item_type']=='question')
                {
                    $info = db('question')->where(['id'=>$v['item_id'],'status'=>1])->field('title,detail')->find();
                    if(!$info) {
                        unset($result[$k]);
                        continue;
                    }
                    $result[$k]['title'] = $info['title'];
                    $result[$k]['label'] = L('问答');
                    $result[$k]['url'] = (string)url('question/detail',['id'=>$v['item_id']]);
                    $result[$k]['content'] = str_cut(strip_tags(htmlspecialchars_decode($info['detail'])),0,200);
                }else if($v['item_type']=='article')
                {
                    $info = db('article')->where(['id'=>$v['item_id'],'status'=>1])->field('title,message')->find();
                    if(!$info) {
                        unset($result[$k]);
                        continue;
                    }
                    $result[$k]['title'] = $info['title'];
                    $result[$k]['label'] = L('文章');
                    $result[$k]['url'] = (string)url('article/detail',['id'=>$v['item_id']]);
                    $result[$k]['content'] = str_cut(strip_tags(htmlspecialchars_decode($info['message'])),0,200);
                }else if($v['item_type']=='column')
                {
                    $info = db('column')->where(['id'=>$v['item_id'],'verify'=>1])->field('name,description')->find();
                    if(!$info) {
                        unset($result[$k]);
                        continue;
                    }
                    $result[$k]['title'] = $info['name'];
                    $result[$k]['label'] = L('专栏');
                    $result[$k]['url'] = (string)url('column/detail',['id'=>$v['item_id']]);
                    $result[$k]['content'] = str_cut($info['description'],0,200);
                }else if($v['item_type']=='topic')
                {
                    $info = db('topic')->where(['id'=>$v['item_id'],'status'=>1])->field('title,description')->find();
                    if(!$info) {
                        unset($result[$k]);
                        continue;
                    }
                    $result[$k]['title'] = $info['title'];
                    $result[$k]['label'] = L('话题');
                    $result[$k]['url'] = (string)url('topic/detail',['id'=>$v['item_id']]);
                    $result[$k]['content'] = str_cut(strip_tags(htmlspecialchars_decode($info['description'])),0,200);
                }else{
                    /*处理其他类型的浏览记录*/
                    hook('browse_records_'.$v['item_type'],$v);
                }
            }
        }

        $result_list['list'] = $result;
        $result_list['page'] = $pageVar;
        $result_list['total'] = $data['last_page'];

        return  $result_list;
    }
}