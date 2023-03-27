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

class ArrayHelper
{
	/**
	 * 移除空值的key
	 * @param $para
	 * @return array
	 * @author helei
	 */
	public static function arrayFilter($para): array
    {
		$paraFilter = [];
		while (list($key, $val) = self::arrayEach($para)) {
			if ($val === '' || $val === null || $val==='undefined') {
				continue;
			}

            if (!is_array($para[$key])) {
                $para[$key] = is_bool($para[$key]) ? $para[$key] : trim($para[$key]);
            }
            $paraFilter[$key] = $para[$key];
        }
		return $paraFilter;
	}

    /**
     * 替代老版本的each函数
     * @param $array
     * @return array
     */
    public static function arrayEach(&$array){
        $res = array();
        $key = key($array);
        if($key !== null){
            next($array);
            $res[1] = $res['value'] = $array[$key];
            $res[0] = $res['key'] = $key;
        }else{
            $res = false;
        }
        return $res;
    }

    /**
     * 数组深度合并 相同KEY值 如果是数组合并 如果是字符串覆盖
     *
     * @param array $original
     * @param array $extend
     * @param bool $main   只处理一级
     * @return array
     */
    public static function extends(array $original, array $extend, $main = false):array
    {
        foreach ($extend as $k => $v) {
            $original[$k] = isset($original[$k]) && is_array($original[$k]) ? ($main ? $v : static::extends($original[$k], (array)$v)) : $v;
        }
        return $original;
    }

    /**
     * 格式化参数值
     *
     * @param $params
     *
     * @return array|bool|float|int|string
     */
    public static function paramsFormat( $params )
    {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $params[$k] = static::paramsFormat($v);
            }
        }else{
            if (is_numeric($params)) {
                if ($params > PHP_INT_MAX) {
                    $params = (string)$params;
                }else{
                    $params = (int)$params;
                }
            }elseif (is_float($params)) {
                $params = (double)$params;
            } elseif (is_bool($params)) {
                $params = (bool)$params;
            } elseif ($params === 'false') {
                $params = false;
            } elseif ($params === 'true') {
                $params = true;
            }
        }
        return $params;
    }

	/**
	 * 删除一位数组中，指定的key与对应的值
	 * @param array $array 要操作的数组
	 * @param array|string $keys 需要删除的key的数组，或者用（,）链接的字符串
	 * @return array
	 */
	public static function removeKeys(array $array, $keys): array
    {
		if (!is_array($keys)) {// 如果不是数组，需要进行转换
			$keys = explode(',', $keys);
		}
		if (empty($keys) || !is_array($keys)) {
			return $array;
		}
		$flag = true;
		foreach ($keys as $key) {
			if (array_key_exists($key, $array)) {
				if (is_int($key)) {
					$flag = false;
				}
				unset($array[$key]);
			}
		}
		if (!$flag) {
			$array = array_values($array);
		}
		return $array;
	}

	/**
	 * 对输入的数组进行字典排序
	 * @param array $array 需要排序的数组
	 * @return array
	 * @author helei
	 */
	public static function arraySort(array $array): array
    {
		ksort($array);
		reset($array);
		return $array;
	}

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param array $array 需要拼接的数组
     * @return string
     * @throws \RuntimeException
     */
	public static function createLinkString(array $array): string
    {
		if (!is_array($array)) {
			throw new \RuntimeException('必须传入数组参数');
		}
		reset($array);
		$arg = '';
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                continue;
            }
            $arg .= $key . '=' . urldecode($val) . '&';
        }
        //去掉最后一个&字符
		$arg && $arg = rtrim($arg, '&');
		//如果存在转义字符，那么去掉转义
		if (get_magic_quotes_gpc()) {
			$arg = stripslashes($arg);
		}
		return $arg;
	}

    /**
     * 格式化数组转普通数组
     *
     * @param array  $options
     * @param string $keyName   作为键的参数
     * @param string $valueName 作为值的参数
     * @return array
     */
    public static function simpleOptions(array $options, $keyName = 'key', $valueName = 'value'): array
    {
        $data = [];
        foreach ($options as $v) {
            $data[$v[$keyName]] = $v[$valueName];
        }
        return $data;
    }

	/**
	 * 解析配置
	 * @param string $value 配置值
	 * @return array
	 */
	public static function parseToArr($value = ''):array
	{
		$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
		if (strpos($value, ':')) {
			$value = array();
			foreach ($array as $val) {
				[$k, $v] = explode(':', $val);
				$value[$k] = $v;
			}
		}
		if (strpos($value, '|')){
            $value = array();
            foreach ($array as $val) {
                [$k, $v] = explode('|', $val);
                $value[$k] = $v;
            }
		}
		return $value;
	}

	/**
	 * 将字符串解析成键值数组.
	 * @param $text
	 * @param string $split
	 * @return array
	 */
	public static function strToArr($text, string $split = "\r\n"): array
    {
		$content = explode($split, $text);
		$arr = [];
		foreach ($content as $k => $v) {
			if (strpos($v, '|') !== false) {
				$item = explode('|', $v);
				$arr[$item[0]] = $item[1];
			}
		}
		return $arr;
	}

	/**
	 * 将键值数组转换为字符串.
	 * @param $array
	 * @param string $split
	 * @return string
	 */
	public static function arrToStr($array, $split = "\r\n"): string
    {
		$content = '';
		if ($array && is_array($array)) {
			$arr = [];
			foreach ($array as $k => $v) {
				$arr[] = "{$k}|{$v}";
			}
			$content = implode($split, $arr);
		}

		return $content;
	}

	/**
	 * 获取配置项数组数据
	 * @param $data
	 * @return array|false
	 */
	public static function getArrayData($data)
	{
		if (! isset($data['value'])) {
			$result = [];
			foreach ($data as $index => $datum) {
				$result['field'][$index] = $datum['key'];
				$result['value'][$index] = $datum['value'];
			}
			$data = $result;
		}
		$fieldArr = $valueArr = [];
		$field = $data['field'] ?? ($data['key'] ?? []);
		$value = $data['value'] ?? [];
		foreach ($field as $m => $n) {
			if ($n) {
				$fieldArr[] = $n;
				$valueArr[] = $value[$m];
			}
		}
		return $fieldArr ? array_combine($fieldArr, $valueArr) : [];
	}

    public static function string2array($info=''): array
    {
        if ($info == '') return [];
        eval("\$r = $info;");
        return $r;
    }

    public static function array2string($info): ?string
    {
        //删除空格，某些情况下字段的设置会出现换行和空格的情况
        if (is_array($info)) {
            if (array_key_exists('options', $info)) {
                $info['options'] = trim($info['options']);
            }
        }
        if ($info == '') return '';

        $string = [];
        if (!is_array($info)) {
            //删除反斜杠
            $string = stripslashes($info);
        }
        foreach ($info as $key => $val) {
            $string[$key] = stripslashes($val);
        }
        return var_export($string, TRUE);
    }

    /**
     * 二维数组根据某些键值去重
     * @param $arr
     * @param $key1
     * @param null $key2
     * @param null $key3
     * @return mixed
     */
    public static function arrayUniqueByKey3($arr,$key1,$key2=null,$key3=null)
    {
        $tmp_arr = [];
        foreach($arr as $k=>$v)
        {
            if(in_array($v[$key1].'-'.$v[$key2].'-'.$v[$key3], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            {
                unset($arr[$k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值
            } else {
                $tmp_arr[$k] = $v[$key1].'-'.$v[$key2].'-'.$v[$key3];  //将不同的值放在该数组中保存
            }
        }
        return $arr;
   }

    /**
     * 二维数组根据某些键值去重
     * @param $arr
     * @param $key1
     * @param null $key2
     * @return mixed
     */
    public static function arrayUniqueByKey2($arr,$key1,$key2=null)
    {
        $tmp_arr = [];
        foreach($arr as $k=>$v)
        {
            if(in_array($v[$key1].'-'.$v[$key2], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            {
                unset($arr[$k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值
            } else {
                $tmp_arr[$k] = $v[$key1].'-'.$v[$key2];  //将不同的值放在该数组中保存
            }
        }
        return $arr;
    }

    /**
     * 一维数组根据某些键值去重
     * @param $arr
     * @return mixed
     */
    public static function arrayUniqueByKey($arr)
    {
        $tmp_arr = [];
        foreach($arr as $k=>$v)
        {
            if(in_array($k, $tmp_arr))
            {
                unset($arr[$k]);
            } else {
                $tmp_arr[$k] = $k;
            }
        }
        return $arr;
    }

    public static function awsASort($source_array, $order_field, $sort_type = 'DESC')
    {
        if (! is_array($source_array) or sizeof($source_array) == 0)
        {
            return false;
        }

        $sort_array = $sorted_array = [];
        foreach ($source_array as $array_key => $array_row)
        {
            $sort_array[$array_key] = $array_row[$order_field];
        }

        $sort_func = ($sort_type == 'ASC' ? 'asort' : 'arsort');

        $sort_func($sort_array);

        // 重组数组
        foreach ($sort_array as $key => $val)
        {
            $sorted_array[$key] = $source_array[$key];
        }

        return $sorted_array;
    }
}