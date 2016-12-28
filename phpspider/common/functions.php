<?php

//自定义函数
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}
function encodeUrl($url){
    $url = preg_replace('/[^0-9a-zA-Z]+/', '-', $url);
    $url = trim($url, '-');
    $url = strtolower($url);
    return $url;
}

function filterString($string){
    return trim(str_replace(["\r", "\n", "\r\n", '&#13;', "\t"], '', $string));
}

function mangaTitle($title){
    $title = trim($title);
    return str_replace([' Other', ' Manga', ' Manhwa', ' Manhua'], '', $title);
}

function mangaType($type){
    $type = trim(str_replace(['&#13;', "\r", "\n", "\r\n", "\t"], '', $type));
    $arr = explode('-', $type);
    if(is_array($arr)){
        $type = trim($arr[0]);
    }
    return $type;
}
function mangaAlternative($alternative){
    $alternative = trim(str_replace(['&#13;', "\r", "\n", "\r\n", "\t"], '', $alternative));
    $arr = explode(';', $alternative);
    if(is_array($arr)){
        $arr = array_map('trim', $arr);
        $alternative = implode(';', $arr);
    }
    return $alternative;
}
function mangaStatus($status){
    $status = trim(str_replace(["\r", "\n", "\r\n", '&#13;', "\t"], '', $status));
    if($status == 'Ongoing' || strpos($status, 'Ongoing') !== false){
        return 0;
    }elseif($status == 'Completed' || strpos($status, 'Completed') !== false){
        return 1;
    }else{
        return -1;
    }
}

function mangaChapter($url, $title = '', $time = ''){
    $ret = [];
    $ret['url'] = $url;

    $url_arr = explode('/', urldecode($url));
    unset($url_arr[0]);
    unset($url_arr[1]);
    unset($url_arr[2]);
    array_pop($url_arr);
    foreach($url_arr as $v){
        $one = substr($v, 0, 1);
        $two = substr($v, 1);
        switch ($one){
            case 's':
                $ret['edition'] = $two;
                break;
            case 'v':
                $ret['volume'] = $two;
                break;
            case 'c':
                $ret['ch'] = $two;
                break;
        }
    }

    if(is_array($title)){
        $title = array_map('trim', $title);
        $ret['title'] = ltrim(implode('', $title), ':');
    }elseif($title){
        $ret['title'] = ltrim(trim($title), ':');
    }
    if($time){
        $time = trim($time);
        $time = str_replace(['am', 'pm'], '', $time);
        $ret['published'] = date('Y-m-d H:i:s', strtotime($time));
    }

    return $ret;
}

function getInitial($str){
    $regx = "/^([a-zA-Z]).*/";
    preg_match($regx, $str, $matchs);
    if(!$matchs){
        return '#';
    }else{
        return strtoupper($matchs[1]);
    }
}

function getImgTableName($chapter_id){
    $i = $chapter_id % 100;
    return 'img_'.$i;
}
