<?php
namespace app\model;

class Help extends BaseModel
{
    /**
     * 获取帮助章节列表
     * @param $page
     * @param $per_page
     * @param $chapters
     * @param $pjax
     * @return array
     */
    public static function getHelpChapterList($page=1,$per_page=10,$chapters=true,$pjax='tabMain'): array
    {
        $list = db('help_chapter')
            ->order('sort', 'ASC')
            ->paginate(
            [
                'list_rows' => $per_page,
                'page' => $page,
                'query' => request()->param(),
                'pjax' => $pjax
            ]
        );
        $pageVar = $list->render();
        $data = $list->toArray();
        if($chapters)
        {
            foreach ($data['data'] as $k=>$v)
            {
                $data['data'][$k]['chapters'] = self::getRelationHelpChapterListByChapterId($v['id'],3,);
            }
        }
        return ['list' => $data['data'], 'page' => $pageVar, 'total' => $data['last_page']];
    }

    private static function getRelationHelpChapterListByChapterId($chapter_id,$limit)
    {
        $list = db('help_chapter_relation')
            ->where(['chapter_id'=>$chapter_id,'status'=>1])
            ->order('sort', 'ASC')
            ->limit($limit)
            ->select()
            ->toArray();

        foreach ($list as $k=>$v)
        {
            if($v['item_type']=='question')
            {
                $list[$k]['info'] = db('question')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }

            if($v['item_type']=='article')
            {
                $list[$k]['info'] = db('article')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }
        }
        return $list;
    }

    public static function getRelationHelpChapterList($chapter_id,$page=1,$per_page=10,$pjax='tabMain')
    {
        $list = db('help_chapter_relation')
            ->where(['chapter_id'=>$chapter_id,'status'=>1])
            ->order('sort', 'ASC')
            ->paginate(
                [
                    'list_rows' => $per_page,
                    'page' => $page,
                    'query' => request()->param(),
                    'pjax' => $pjax
                ]
            );
        $pageVar = $list->render();
        $data = $list->toArray();
        $dataList=$data['data'];
        foreach ($dataList as $k=>$v)
        {
            if($v['item_type']=='question')
            {
                $dataList[$k]['info'] = db('question')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }

            if($v['item_type']=='article')
            {
                $dataList[$k]['info'] = db('article')->where(['id'=>$v['item_id'],'status'=>1])->find();
            }
        }

        return ['list' => $dataList, 'page' => $pageVar, 'total' => $data['last_page']];
    }

    /**
     * 检查是否已加入帮助
     */
    public static function checkRelationHelpItemExist($chapter_id,$item_id,$item_type)
    {
        if(!$chapter_id || !$item_type || !$item_id) return false;
        return  db('help_chapter_relation')->where(['chapter_id'=>$chapter_id,'status'=>1,'item_id'=>$item_id,'item_type'=>$item_type])->value('id');
    }
}