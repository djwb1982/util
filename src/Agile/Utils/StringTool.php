<?php
/**
 * StringTool 
 * 用于处理字符串的方法
 */
namespace Agile\Utils;
class StringTool
{
    /**
     * 过滤非法字符
     * @param   string $str <p>需要过滤的字符串</p>
     * @param   array $filterwords <p>包含非法字符串的数组</p>
     * @param   int $size <p>将非法字符串的数组分块处理的大小</p>
     * @return  string               <p>返回匹配的第一个非法字符串</p>
     */
    public static function filter($str, $filterwords, $size = 100)
    {
        $stopword = '';
        $pattern = '';
        if (is_array($filterwords) && !empty($filterwords)) {
            $wordsArr = array_chunk($filterwords, $size);
            foreach ($wordsArr as $row) {
                $matches = [];
                $pattern = implode("|", $row);
                if (preg_match("/{$pattern}/i", $str, $matches)) {
                    $stopword = $matches[0];
                    break;
                }
            }
        }
        return $stopword;
    }

    /**
     * 获取UTF-8格式的字符串长度
     * @param   string $str <p>待检测的字符串</p>
     * @return  int           <p>字符串<i>str</i>的长度，其中多字节的字符计1个长度</p>
     */
    public static function strlenUtf8($str)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'utf-8');
        } else {
            $ar = [];
            preg_match_all("/./u", $str, $ar);
            return count($ar[0]);
        }
    }

    /**
     * 获取一个随机字符串(排除了易混淆的字符)
     * @param   int $length <p>要获取的字符串的长度</p>
     * @return  string           <p>返回的随机字符串</p>
     */
    public static function randStr($length = 6)
    {
        $str = '';
        $chars = 'ABDEFGHJKLMNPQRSTVWXYabdefghijkmnpqrstvwxy23456789';
        $randLen = strlen($chars) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= substr($chars, rand(0, $randLen), 1);
        }
        return $str;
    }

    /**
     * 以UTF-8格式截取字符串
     * @param string $str <p>待截取的字符串</p>
     * @param int $start <p>从字符串<i>str</i>开始截取的位置</p>
     * @param int $length <p>从字符串<i>str</i>截取的长度，如果超长或为NULL,将返回相同的字符串</p>
     * @return string
     */
    public static function substrUtf8($str, $start, $length = null)
    {
        if (empty($length)) {
            return $str;
        }
        if (function_exists('mb_substr')) {
            return mb_substr($str, $start, $length, 'UTF-8');
        } else {
            $matches = [];
            preg_match_all("/./su", $str, $matches);
            return join("", array_slice($matches[0], $start, $length));
        }
    }

    /**
     * 移除所有不可见字符
     * @param string $string <p>待处理的字符串</p>
     * @return string           <p>处理后的字符串</p>
     */
    public static function trimInvisible($string)
    {
        $newstr = '';
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $ascii = ord($string[$i]);
            //不可见字符
            if ($ascii <= 31 || $ascii == 127) {
                continue;
            }
            $newstr .= $string[$i];
        }
        return $newstr;
    }

    /**
     * 替换字符串
     * @param string $search <p>需要被替换的字符串</p>
     * @param string $replace <p>替换的字符串</p>
     * @param mixed $data <p>要处理的数据，可以是字符串或包含字符串的数组</p>
     * @return mixed            <p>处理后的数据</p>
     */
    static public function replace($search, $replace, $data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::replace($search, $replace, $value);
            }
        } elseif (is_string($data)) {
            $data = str_replace($search, $replace, $data);
        }
        return $data;
    }

    /**
     * 生成随机码
     *
     * @param int $length
     * @param bool|false $intmode
     * @return string
     */
    static function random($length, $intmode = false)
    {
        $hash = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $intmode and $chars = "0123456789";
        $max = strlen($chars) - 1;
        PHP_VERSION < '4.2.0' && mt_srand(( double )microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars [mt_rand(0, $max)];
        }
        return $hash;
    }

    //防止跨站攻击
    static public function removeXss($val)
    {
        $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search.= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search.= '1234567890!@#$%^&*()';
        $search.= '~`";:?+/={}[]-_|\'\\';

        for ($i = 0; $i < strlen($search); $i++) {
            $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val);
        }

        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta','blink', 'link',  'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint',
            'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged',
            'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange',
            'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave',
            'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize',
            'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true;
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                        $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                        $pattern .= ')?';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
                $val = preg_replace($pattern, $replacement, $val);
                if ($val_before == $val) {
                    $found = false;
                }
            }
        }
        return $val;
    }

    /**
     * @param Request $request
     * @return mixed截取字符串
     */
    static  function cc_msubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr")) {
            return mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            return iconv_substr($str, $start, $length, $charset);
        }
        $re['utf-8'] = "/[/x01-/x7f]|[/xc2-/xdf][/x80-/xbf]|[/xe0-/xef][/x80-/xbf]{2}|[/xf0-/xff][/x80-/xbf]{3}/";
        $re['gb2312'] = "/[/x01-/x7f]|[/xb0-/xf7][/xa0-/xfe]/";
        $re['gbk'] = "/[/x01-/x7f]|[/x81-/xfe][/x40-/xfe]/";
        $re['big5'] = "/[/x01-/x7f]|[/x81-/xfe]([/x40-/x7e]|/xa1-/xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
        if ($suffix) {
            return $slice . "..";
        } else {
            return $slice;
        }
    }
}