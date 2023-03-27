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

namespace app\common\library\helper;

class TreeHelper
{
	protected static $instance;
	//默认配置
	protected $config = [];
	public $options = [];

	/**
	 * 生成树型结构所需要的2维数组.
	 *
	 * @var array
	 */
	public $arr = [];

	/**
	 * 生成树型结构所需修饰符号，可以换成图片.
	 * @var array
	 */
	public $icon = ['│', '├', '└'];
	public $nbsp = '&nbsp;';
	public $pidName = 'pid';

	public function __construct($options = [])
	{
		$this->options = array_merge($this->config, $options);
	}

	/**
	 * 初始化
	 * @param array $options
	 * @return TreeHelper
	 */
	public static function instance(array $options = []): TreeHelper
    {
		if (is_null(self::$instance)) {
			self::$instance = new static($options);
		}
		return self::$instance;
	}

    /**
     * 初始化方法.
     * @param array $arr 2维数组，例如：
     *                        array(
     *                        1 => array('id'=>'1','pid'=>0,'name'=>'一级栏目一'),
     *                        2 => array('id'=>'2','pid'=>0,'name'=>'一级栏目二'),
     *                        3 => array('id'=>'3','pid'=>1,'name'=>'二级栏目一'),
     *                        4 => array('id'=>'4','pid'=>1,'name'=>'二级栏目二'),
     *                        5 => array('id'=>'5','pid'=>2,'name'=>'二级栏目三'),
     *                        6 => array('id'=>'6','pid'=>3,'name'=>'三级栏目一'),
     *                        7 => array('id'=>'7','pid'=>3,'name'=>'三级栏目二')
     *                        )
     * @param string|null $pidName 父字段名称
     * @param string|null $nbsp 空格占位符
     * @return $this
     */
	public function init(array $arr = [],string $pidName = null,string $nbsp = null): TreeHelper
    {
		$this->arr = $arr;
		if (! is_null($pidName)) {
			$this->pidName = $pidName;
		}
		if (! is_null($nbsp)) {
			$this->nbsp = $nbsp;
		}

		return $this;
	}

	/**
	 * 得到子级数组.
	 * @param int $myId
     * @return array
	 */
	public function getChild(int $myId): array
    {
		$newArr = [];
		foreach ($this->arr as $value) {
			if (! isset($value['id'])) {
				continue;
			}
			if ($value[$this->pidName] == $myId) {
				$newArr[$value['id']] = $value;
			}
		}
		return $newArr;
	}

	/**
	 * 读取指定节点的所有孩子节点.
	 * @param int  $myId     节点ID
	 * @param bool $withSelf 是否包含自身
	 * @return array
	 */
	public function getChildren(int $myId,bool $withSelf = false): array
    {
		$newArr = $result =  [];
		foreach ($this->arr as $value) {
			if (! isset($value['id'])) {
				continue;
			}
			if ($value[$this->pidName] == $myId) {
				$newArr[] = $value;
                $result = array_merge($newArr, $this->getChildren($value['id']));
			} elseif ($withSelf && $value['id'] == $myId) {
				$newArr[] = $value;
                $result = $newArr;
			}
		}
		return $result;
	}

	/**
	 * 读取指定节点的所有孩子节点ID.
	 * @param int  $myId     节点ID
	 * @param bool $withSelf 是否包含自身
	 * @return array
	 */
	public function getChildrenIds(int $myId,bool $withSelf = false): array
    {
		$childrenList = $this->getChildren($myId, $withSelf);
		$childrenIds = [];
		foreach ($childrenList as $k => $v) {
			$childrenIds[] = $v['id'];
		}
		return $childrenIds;
	}

	/**
	 * 得到当前位置父辈数组.
	 * @param int
	 * @return array
	 */
	public function getParent($myId): array
    {
		$pid = 0;
		$newArr = [];
		foreach ($this->arr as $value) {
			if (! isset($value['id'])) {
				continue;
			}
			if ($value['id'] == $myId) {
				$pid = $value[$this->pidName];
				break;
			}
		}
		if ($pid) {
			foreach ($this->arr as $value) {
				if ($value['id'] == $pid) {
					$newArr[] = $value;
					break;
				}
			}
		}
		return $newArr;
	}

	/**
	 * 得到当前位置所有父辈数组.
	 * @param int $myId
     * @param bool $withSelf 是否包含自己
	 * @return array
	 */
	public function getParents(int $myId,bool $withSelf = false): array
    {
		$pid = 0;
		$newArr = [];
		foreach ($this->arr as $value) {
			if (! isset($value['id'])) {
				continue;
			}
			if ($value['id'] == $myId) {
				if ($withSelf) {
					$newArr[] = $value;
				}
				$pid = $value[$this->pidName];
				break;
			}
		}
		if ($pid) {
			$arr = $this->getParents($pid, true);
			$newArr = array_merge($arr, $newArr);
		}
		return $newArr;
	}

	/**
	 * 读取指定节点所有父类节点ID.
	 * @param int  $myId
	 * @param bool $withSelf
	 * @return array
	 */
	public function getParentsIds(int $myId,bool $withSelf = false): array
    {
		$parentList = $this->getParents($myId, $withSelf);
		$parentsIds = [];
		foreach ($parentList as $k => $v) {
			$parentsIds[] = $v['id'];
		}

		return $parentsIds;
	}

	/**
	 * 树型结构Option.
	 * @param int    $myId        表示获得这个ID下的所有子级
	 * @param string $itemTpl     条目模板 如："<option value=@id @selected @disabled>@spacer@name</option>"
	 * @param mixed  $selectedIds 被选中的ID，比如在做树型下拉框的时候需要用到
	 * @param mixed  $disabledIds 被禁用的ID，比如在做树型下拉框的时候需要用到
	 * @param string $itemprefix  每一项前缀
	 * @param string $toptpl      顶级栏目的模板
	 * @return string
	 */
	public function getTree($myId, $itemTpl = '<option value=@id @selected @disabled>@spacer@name</option>', $selectedIds = '', $disabledIds = '', $itemprefix = '', $toptpl = ''): string
    {
		$ret = '';
		$number = 1;
		$childs = $this->getChild($myId);
		if ($childs) {
			$total = count($childs);
			foreach ($childs as $value) {
				$id = $value['id'];
				$j = $k = '';
				if ($number == $total) {
					$j .= $this->icon[2];
					$k = $itemprefix ? $this->nbsp : '';
				} else {
					$j .= $this->icon[1];
					$k = $itemprefix ? $this->icon[0] : '';
				}
				$spacer = $itemprefix ? $itemprefix.$j : '';
				$selected = $selectedIds && in_array($id, (is_array($selectedIds) ? $selectedIds : explode(',', $selectedIds))) ? 'selected' : '';
				$disabled = $disabledIds && in_array($id, (is_array($disabledIds) ? $disabledIds : explode(',', $disabledIds))) ? 'disabled' : '';
				$value = array_merge($value, ['selected' => $selected, 'disabled' => $disabled, 'spacer' => $spacer]);
				$value = array_combine(array_map(function ($k) {
					return '@'.$k;
				}, array_keys($value)), $value);
				$nstr = strtr((($value["@{$this->pidName}"] == 0 || $this->getChild($id)) && $toptpl ? $toptpl : $itemTpl), $value);
				$ret .= $nstr;
				$ret .= $this->getTree($id, $itemTpl, $selectedIds, $disabledIds, $itemprefix.$k.$this->nbsp, $toptpl);
				$number++;
			}
		}

		return $ret;
	}

	/**
	 * 树型结构UL.
	 *
	 * @param int    $myId        表示获得这个ID下的所有子级
	 * @param string $itemTpl     条目模板 如："<li value=@id @selected @disabled>@name @childlist</li>"
	 * @param string $selectedIds 选中的ID
	 * @param string $disabledIds 禁用的ID
	 * @param string $wrapTag     子列表包裹标签
	 * @param string $wrapAttr    子列表包裹属性
	 *
	 * @return string
	 */
	public function getTreeUl($myId, $itemTpl, $selectedIds = '', $disabledIds = '', $wrapTag = 'ul', $wrapAttr = ''): string
    {
		$str = '';
		$childs = $this->getChild($myId);
		if ($childs) {
			foreach ($childs as $value) {
				$id = $value['id'];
				unset($value['child']);
				$selected = $selectedIds && in_array($id, (is_array($selectedIds) ? $selectedIds : explode(',', $selectedIds))) ? 'selected' : '';
				$disabled = $disabledIds && in_array($id, (is_array($disabledIds) ? $disabledIds : explode(',', $disabledIds))) ? 'disabled' : '';
				$value = array_merge($value, ['selected' => $selected, 'disabled' => $disabled]);
				$value = array_combine(array_map(function ($k) {
					return '@'.$k;
				}, array_keys($value)), $value);
				$nstr = strtr($itemTpl, $value);
				$childData = $this->getTreeUl($id, $itemTpl, $selectedIds, $disabledIds, $wrapTag, $wrapAttr);
				$childList =  $childData ? "<{$wrapTag} {$wrapAttr}>". $childData."</{$wrapTag}>" : '';
				$str .= strtr($nstr, ['@childList' => $childList]);
			}
		}

		return $str;
	}

    /**
     * 菜单数据.
     * @param $myid
     * @param $itemtpl
     * @param array $selectedids
     * @param string $disabledids
     * @param string $wraptag
     * @param string $wrapattr
     * @param int $deeplevel
     * @return string
     */
    public function getTreeMenu($myid, $itemtpl, $selectedids = '', $disabledids = '', $wraptag = 'ul', $wrapattr = '', $deeplevel = 0)
    {
        $str = '';
        $childs = $this->getChild($myid);
        if ($childs) {
            foreach ($childs as $value) {
                $id = $value['id'];
                $pid_ids = $this->getParentsIds($id);
                unset($value['child']);
                $selected = in_array($id, (is_array($selectedids) ? $selectedids : explode(',', $selectedids))) ? 'selected' : '';
                $disabled = in_array($id, (is_array($disabledids) ? $disabledids : explode(',', $disabledids))) ? 'disabled' : '';
                $treeview = in_array($value['pid'], $pid_ids);
                $value = array_merge($value, array('selected' => $selected, 'disabled' => $disabled));
                $value = array_combine(array_map(function ($k) {
                    return '@' . $k;
                }, array_keys($value)), $value);
                $bakvalue = array_intersect_key($value, array_flip(['@url', '@caret', '@class']));
                $value = array_diff_key($value, $bakvalue);
                $nstr = strtr($itemtpl, $value);
                $value = array_merge($value, $bakvalue);
                $childdata = $this->getTreeMenu($id, $itemtpl, $selectedids, $disabledids, $wraptag, $wrapattr, $deeplevel + 1);
                $childlist = $childdata ? "<{$wraptag} {$wrapattr}>" . $childdata . "</{$wraptag}>" : "";
                $childlist = strtr($childlist, array('@class' => $childdata ? 'last' : ''));
                $value = array(
                    '@childlist' => $childlist,
                    '@url'       => $childdata || !isset($value['@url']) ? "javascript:;" : $value['@url'],
                    '@caret'     => ($childdata && (!isset($value['@badge']) || !$value['@badge']) ? '<i class="fa fa-angle-left"></i>' : ''),
                    '@class'     =>  $childdata && $treeview ? ' menu-is-opening menu-open' : '',
                    '@current'   =>($selected ? ' active' : '') . ($disabled ? ' disabled' : '')
                );
                $str .= strtr($nstr, $value);
            }
        }
        return $str;
    }


    /**
	 * 特殊.
	 * @param int    $myId        要查询的ID
	 * @param string $itemTpl1    第一种HTML代码方式
	 * @param string $itemTpl2    第二种HTML代码方式
	 * @param mixed  $selectedIds 默认选中
	 * @param mixed  $disabledIds 禁用
	 * @param string $itemprefix  前缀
	 * @return string
	 */
	public function getTreeSpecial($myId, $itemTpl1, $itemTpl2, $selectedIds = 0, $disabledIds = 0, $itemprefix = '')
	{
		$ret = '';
		$number = 1;
		$childs = $this->getChild($myId);
		if ($childs) {
			$total = count($childs);
			foreach ($childs as $id => $value) {
				$j = $k = '';
				if ($number == $total) {
					$j .= $this->icon[2];
					$k = $itemprefix ? $this->nbsp : '';
				} else {
					$j .= $this->icon[1];
					$k = $itemprefix ? $this->icon[0] : '';
				}
				$spacer = $itemprefix ? $itemprefix.$j : '';
				$selected = $selectedIds && in_array($id, (is_array($selectedIds) ? $selectedIds : explode(',', $selectedIds))) ? 'selected' : '';
				$disabled = $disabledIds && in_array($id, (is_array($disabledIds) ? $disabledIds : explode(',', $disabledIds))) ? 'disabled' : '';
				$value = array_merge($value, ['selected' => $selected, 'disabled' => $disabled, 'spacer' => $spacer]);
				$value = array_combine(array_map(function ($k) {
					return '@'.$k;
				}, array_keys($value)), $value);
				$nstr = strtr(! isset($value['@disabled']) || ! $value['@disabled'] ? $itemTpl1 : $itemTpl2, $value);

				$ret .= $nstr;
				$ret .= $this->getTreeSpecial($id, $itemTpl1, $itemTpl2, $selectedIds, $disabledIds, $itemprefix.$k.$this->nbsp);
				$number++;
			}
		}

		return $ret;
	}

	/**
	 * 获取树状数组.
	 * @param string $myId       要查询的ID
	 * @param string $itemprefix 前缀
	 * @return array
	 */
	public function getTreeArray($myId, $itemprefix = '')
	{
		$childs = $this->getChild($myId);
		$n = 0;
		$data = [];
		$number = 1;
		if ($childs) {
			$total = count($childs);
			foreach ($childs as $id => $value) {
				$j = $k = '';
				if ($number == $total) {
					$j .= $this->icon[2];
					$k = $itemprefix ? $this->nbsp : '';
				} else {
					$j .= $this->icon[1];
					$k = $itemprefix ? $this->icon[0] : '';
				}
				$spacer = $itemprefix ? $itemprefix.$j : '';
				$value['spacer'] = $spacer;
				$data[$n] = $value;
				$data[$n]['child_list'] = $this->getTreeArray($id, $itemprefix.$k.$this->nbsp);
				$n++;
				$number++;
			}
		}

		return $data;
	}

	/**
	 * 将getTreeArray的结果返回为二维数组.
	 *
	 * @param array  $data
	 * @param string $field
	 *
	 * @return array
	 */
	public function getTreeList($data = [], $field = 'name')
	{
		$arr = $result = [];
		foreach ($data as $k => $v) {
			$childlist = isset($v['childlist']) ? $v['childlist'] : [];
			unset($v['childlist']);
			$v[$field] = $v['spacer'].' '.$v[$field];
			$v['haschild'] = $childlist ? 1 : 0;
			if ($v['id']) {
				$arr[] = $v;
			}
            $result = $arr;
			if ($childlist) {
                $result = array_merge($arr, $this->getTreeList($childlist, $field));
			}
		}
		return $result;
	}

    /**
     * 无限分类-权限
     * @param $cate
     * @param string $leftHtml 分隔符
     * @param int $pid         父ID
     * @param int $lvl         层级
     * @return array
     */
    public static function treeThree($cate , $leftHtml = '|— ' , $pid = 0 , $lvl = 0 ): array
    {
        $arr = $result = array();
        foreach ($cate as $v){
            $keys = array_keys($v);
            if (end($v) == $pid) {
                $v['lvl']      = $lvl + 1;
                $v['left_html'] = str_repeat($leftHtml,$lvl);
                $v[$keys[1]] = $v['left_html'] . $v[$keys[1]];
                $arr[] = $v;
                $result = $arr;
                $treeArr = self::treeThree($cate, $leftHtml, $v[$keys[0]], $lvl+1);
                if(!empty($treeArr))
                {
                    $result = array_merge($arr,$treeArr);
                }
            }
        }
        return $result;
    }

    /**
     * 无限分类-权限
     * @param $cate
     * @param string $leftHtml 分隔符
     * @param int $pid 父ID
     * @param int $lvl 层级
     * @param string $title
     * @return array
     */
    public static function tree($cate , $leftHtml = '|— ' , $pid = 0 , $lvl = 0,$title='title'): array
    {
        $arr = array();
        foreach ($cate as $v){
            if ($v['pid'] == $pid) {
                $v['lvl']      = $lvl + 1;
                $v['left_html'] = str_repeat($leftHtml,$lvl);
                $v['left_title']   = $v['left_html'].$v[$title];
                $arr[] = $v;
                $arr = array_merge($arr,self::tree($cate, $leftHtml, $v['id'], $lvl+1,$title));
            }
        }
        return $arr;
    }

    /**
     * 无限分类-选择
     * @param $cate
     * @param $leftHtml
     * @param $pid
     * @param $lvl
     * @param $name
     * @return array
     */
    public static function treeCate($cate, $leftHtml = '|— ', $pid = 0, $lvl = 0,$name='cate_name')
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['left_html'] = str_repeat($leftHtml, $lvl);
                $v['l_cate_name'] = $v['left_html'] . $name;
                $arr[] = $v;
                $arr = array_merge($arr, self::treeCate($cate, $leftHtml, $v['id'], $lvl + 1));
            }
        }
        return $arr;
    }
}