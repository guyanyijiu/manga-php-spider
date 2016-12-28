<?php
ini_set("memory_limit", "1024M");
require __DIR__ . '/../../phpspider/core/init.php';


$configs = array(
    'name' => 'manga',
    'input_encoding' => 'UTF-8',
    'output_encoding' => 'UTF-8',
    //'log_show' => true,
    'tasknum' => 10,
    'save_running_state' => true,
    'timeout' => 300,
    'max_try' => 3,
    'max_depth' => 0,
    'max_fields' => 0,
    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36',
    'domains' => array(
        'mangapark.me',
    ),
    //入口
    'scan_urls' => array(
        'http://mangapark.me/'
    ),

);

$spider = new phpspider($configs);

$spider->on_start = function ($phpspider){
    requests::set_header("Referer", "http://mangapark.me/");
    requests::set_header("Accept-Language", "zh-CN,zh;q=0.8");
    requests::set_header("Accept-Encoding", "gzip, deflate, sdch");
    requests::set_header("Connection", "keep-alive");
    requests::set_header("Upgrade-Insecure-Requests", "1");
    requests::set_header("Accept", "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8");
    requests::set_cookies('__cfduid=d4033a78cfbb0fe5aaa3b7ec022de39241480056273; cf_clearance=8be45eb2d9410a56b420ad368c7370b69e5273dd-1480059098-31536000; _svt=1480059099; _lad=6; __utma=164524451.606109834.1480059101.1480069289.1480137780.3; __utmb=164524451.0.10.1480137780; __utmc=164524451; __utmz=164524451.1480059101.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); Hm_lvt_5ee99fa43d3e817978c158dfc8eb72ad=1480059101; Hm_lpvt_5ee99fa43d3e817978c158dfc8eb72ad=1480137850; __atuvc=9%7C47; __atuvs=58391c7acb2d6029000');
};

$spider->on_status_code = function($status_code, $url, $content, $phpspider) {
    if($status_code != '200'){
        return false;
    }
    return $content;
};

$spider->on_scan_page = function($page, $content, $phpspider){
    if($page['url'] == 'http://mangapark.me/'){
        $chapters = db::get_all('select url from chapter where status = 0');
        if($chapters){
            foreach ($chapters as $chapter){
                $phpspider->add_scan_url('http://mangapark.me' . substr($chapter['url'], 0, -2));
            }
        }
        return false;
    }

    $url = substr($page['url'], 19);
    $url = $url . '/1';
    $chapter_id = db::get_one("select id from chapter where url = '$url'");

    if($chapter_id){
        $chapter_id = $chapter_id['id'];
        $imgTable = getImgTableName($chapter_id);
        $imgs_selector = "//section[@id='viewer']//img[@class='img']/@src";
        $imgs = selector::select($content, $imgs_selector);
        if($imgs){
            $imgs = (array) $imgs;
            $img_count = count($imgs);
            foreach ($imgs as $img){
                db::insert($imgTable, ['chapter_id' => $chapter_id, 'img' => $img]);
            }
            db::query("update chapter set status = 1,img_count = $img_count where id = $chapter_id");
        }
    }
    return false;
};

$spider->start();