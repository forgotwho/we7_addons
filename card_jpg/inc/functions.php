<?php

/**
 * 简单日志工具
 * @param $text
 */
if (!function_exists('xlog')) {
    function xlog($text) {
        if (!EVAL_DEBUG) {
            return;
        }
        global $logIndex;
        $text = '';
        $params = func_get_args();
        foreach ($params as $v) {
            if (is_array($v)) {
                $v = var_export($v, true);
            }
            $text .= $v . PHP_EOL;
        }
        $logIndex = $logIndex ? $logIndex : 1;
        $logPath = EVAL_LOG_PATH ? EVAL_LOG_PATH : '';
        $log = '';
        if ($logIndex == 1) {
            $log = '==========================华丽分割线==========================' . PHP_EOL;
            $log .= date("Y-m-d H:i:s", time()) . PHP_EOL;
        }
        $logPath = $logPath . 'logs/';
        if (!is_dir($logPath)) {
            xmkdirs($logPath);
        }
        $logFile = $logPath . '/log' . date("Y-m-d", time()) . '.log';
        if (is_array($text)) {
//            foreach($text as $k=>$v);
            $log = $logIndex . ', array==>';
            $text = var_export($text, true);
        } else {
            $log .= $logIndex . ', str==>';
        }
        $log .= $text . PHP_EOL;
        file_put_contents($logFile, $log, FILE_APPEND);
        $logIndex++;
    }
}


if (!function_exists('xmkdirs')) {
    function xmkdirs($dir) {
        if (!is_dir($dir)) {
            if (!xmkdirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir, 0777)) {
                return false;
            }
        }
        return true;
    }
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey   加密EKY
 * @return String
 */
function lhb_encode($string = '', $skey = 'lhbtool') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key] .= $value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey   解密KEY
 * @return String
 */
function lhb_decode($string = '', $skey = 'lhbtool') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}


function replace_url($url, $param = array()) {
    $config = parse_url($url);
    if (!empty($param)) {
        foreach ($param as $k => $v) {
            if (key_exists($k, $config)) {
                $config[$k] = $v;
            }
        }
    }
    return unparse_url($config);
}
/**
 * 解析得来
 * @param $parsed_url parse_url 解析得来
 * @return string
 *                    scheme - 如 http
 *                    host
 *                    port
 *                    user
 *                    pass
 *                    path
 *                    query - 在问号 ? 之后
 *                    fragment - 在散列符号 # 之后
 *                    $url = 'http://usr:pss@example.com:81/mypath/myfile.html?a=b&b[]=2&b[]=3#myfragment';
 */
function unparse_url($parsed_url) {
    $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
    $pass = ($user || $pass) ? "$pass@" : '';
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
    return "$scheme$user$pass$host$port$path$query$fragment";
}
//---test---
function show($var){
    echo '<pre>';
    print_r($var);
}

function checkweixin(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MicroMessenger') === false) {
        return false;
    }else{
        return true;
    }
}
/*1111*/