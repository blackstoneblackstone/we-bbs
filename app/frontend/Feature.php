<?php
namespace app\frontend;

use app\common\controller\Frontend;
use app\model\Feature as FeatureModel;

class Feature extends Frontend
{
    public function index()
    {
        $data = FeatureModel::getFeatureList();
        $this->assign($data);
        return $this->fetch();
    }

    public function detail()
    {
        $token = $this->request->param('token','','trim');
        $sort = $this->request->param('sort','new','trim');

        $info = db('feature')->where(['url_token'=>$token])->find();

        $topic_ids = db('feature_topic')
            ->where(['feature_id'=>$info['id']])
            ->column('topic_id');

        $topics = \app\model\Topic::getTopicByIds($topic_ids);

        $this->assign([
            'info'=>$info,
            'sort'=>$sort,
            'topics'=>$topics
        ]);
        return $this->fetch();
    }
}