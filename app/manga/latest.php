<?php
ini_set("memory_limit", "1024M");
require __DIR__ . '/../../phpspider/core/init.php';

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => 'manga',
    'input_encoding' => 'UTF-8',
    'output_encoding' => 'UTF-8',
    'log_show' => true,
	'log_type' => 'error,warn',
    'tasknum' => 1,
    'save_running_state' => false,
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
        'http://mangapark.me/latest'
    ),
    //列表页
    'list_url_regexes' => array(
        "http://mangapark.me/latest/\w+\/\d+"
    ),
    //内容页
    'content_url_regexes' => array(
        "http://mangapark.me/manga/\w+",
    ),
    //'export' => array(
    //'type' => 'csv',
    //'file' => PATH_DATA.'/qiushibaike.csv',
    //),
    //'export' => array(
    //'type'  => 'sql',
    //'file'  => PATH_DATA.'/qiushibaike.sql',
    //'table' => 'content',
    //),
//    'export' => array(
//        'type' => 'db',
//        'table' => 'manga',
//    ),

    'fields' => array(
        array(
            'name' => "title",
            'selector' => "//html/body/section/div/div[1]/h1/a",
            'required' => true,
        ),
        array(
            'name' => "url_title",
            'selector' => "//html/body/section/div/div[1]/h1/a",
            'required' => true,
        ),
        array(
            'name' => "alternative",
            'selector' => "//section/div//table[@class='outer']//table[@class='attr']//tr[4]/td",
            'required' => false,
        ),
        array(
            'name' => 'summary',
            'selector' => "//html/body/section/div/p",
            'required' => false,
        ),
        array(
            'name' => "author",
            'selector' => "//section/div//table[@class='outer']//table[@class='attr']//tr[5]/td/a",
            'required' => false,
        ),
        array(
            'name' => "artist",
            'selector' => "//section/div//table[@class='outer']//table[@class='attr']//tr[6]/td/a",
            'required' => false,
        ),
        array(
            'name' => "status",
            'selector' => "//section/div//table[@class='outer']//table[@class='attr']//tr[10]/td",
            'required' => false,
        ),
        array(
            'name' => "released",
            'selector' => "//section/div//table[@class='outer']//table[@class='attr']//tr[9]/td",
            'required' => false,
        ),
        array(
            'name' => "score",
            'selector' => "//*[@id=\"rating\"]/i",
            'required' => false,
        ),
        array(
            'name' => "cover180",
            'selector' => "//div[@class='cover']/img/@src",
            'required' => false,
        ),
        array(
            'name' => "type",
            'selector' => "//section/div//table[@class='outer']//table[@class='attr']//tr[8]/td",
            'required' => false,
        ),
        array(
            'name' => "url",
            'selector' => "//div[@class='hd']//a/@href",
            'required' => false,
        ),
    ),
);

$spider = new phpspider($configs);

$spider->on_start = function ($phpspider){
//    var_dump(mangaChapter('/manga/kataomoi-to-parade/s5/c5.5/1', ['      ', "\n", "\r", " \r\n "], 'Sep 30, 2016, 14:04 '));
//    exit;
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

$spider->on_download_page = function($page, $phpspider){
    if($page['url'] == 'http://mangapark.me/latest'){
        $genres_selector = "//div[@id='top-genres']/ul/li/a/@href";
        $genres_urls = selector::select($page['raw'], $genres_selector);
        unset($genres_urls[0]);
        foreach ($genres_urls as $v){
            $phpspider->add_scan_url('http://mangapark.me'.$v);
        }
    }
    return $page;
};

$spider->on_scan_page = function($page, $content, $phpspider)
{
    if($page['url'] == 'http://mangapark.me/latest'){
        return false;
    }
    // 抓取image
    //http://mangapark.me/manga/full-dozer-komura-ayumi/s5/c17.5
    $regex = "/http:\/\/mangapark.me\/manga\/.+\/.+\/.*/";
    preg_match($regex, $page['url'], $matches);
    if($matches){
        //抓取image
        $url = substr($page['url'], 19);
        $url = $url . '/1';
        $chapter_id = db::get_one("select id from chapter where url = '$url'");
        if($chapter_id){
            $chapter_id = $chapter_id['id'];
            $imgTable = getImgTableName($chapter_id);
            $imgs_selector = "//section[@id='viewer']//img[@class='img']/@src";
            $imgs = selector::select($content, $imgs_selector);
            $img_count = 0;
            if($imgs){
                $imgs = (array) $imgs;
                $img_count = count($imgs);
                foreach ($imgs as $img){
                    db::insert($imgTable, ['chapter_id' => $chapter_id, 'img' => $img]);
                }
                db::query("update chapter set status = 1, img_count = $img_count  where id = $chapter_id");
            }
        }else{
            log::warn('img error :'.$page['url']);
        }
        return false;
    }

    // 添加列表页
    $url_selector = "//div[@id='paging-bar']//select/option/@value";
    $urls = selector::select($content, $url_selector);
    if($urls){
        $urls = (array) $urls;
        foreach($urls as $k => $v){
            if($k > 0 && $v = $urls[0]){
                break;
            }
            $phpspider->add_url('http://mangapark.me'.$v);
        }
    }
    return false;
};

//$spider->on_list_page = function ($page, $content, $phpspider){
//
//};

$spider->on_fetch_url = function ($url, $phpspider){
    $regex = '/^http:\/\/mangapark.me\/manga\/[\w-]+$/i';
    preg_match($regex, $url, $matches);
    if(!$matches){
        return false;
    }
    return $url;
};

$spider->on_extract_field = function($fieldname, $data, $page){
    if($fieldname == 'title'){
        $data = mangaTitle($data);
    }
    if($fieldname == 'status'){
        $data = mangaStatus($data);
    }
    if($fieldname == 'type'){
        $data = mangaType($data);
    }
    if($fieldname == 'alternative'){
        $data = mangaAlternative($data);
    }
    return $data;
};

$spider->on_extract_page = function ($page, $data, $phpspider){
    //章节内的选择器
    $chapter_url_selector = "//span/a/@href";
    $chapter_title_selector = "//span/text()";
    $chapter_time_selector = "//i";
    $data['url_title'] = encodeUrl($data['title']);

    $manga = db::get_one("select id from manga where url_title = '{$data['url_title']}'");

    if($manga){  //已存在manga更新
        $latest_selector = "//ul[@class='chapter']/li[@class='new']";
        $latests = selector::select($page['raw'], $latest_selector);
        if($latests){
            $latests = (array) $latests;
            foreach ($latests as $latest){
                $chapter_url = selector::select($latest, $chapter_url_selector);
                $chapter_url = urldecode($chapter_url);
                if(! db::get_one("select id from chapter where url = '$chapter_url'")){
                    $chapter_title = selector::select($latest, $chapter_title_selector);
                    $chapter_time = selector::select($latest, $chapter_time_selector);

                    $insert = mangaChapter($chapter_url, $chapter_title, $chapter_time);
                    $insert['manga_id'] = $manga['id'];
                    $chapter_id = db::insert('chapter', $insert);
                    //准备取图片
                    if($chapter_id){
                        $phpspider->add_scan_url('http://mangapark.me' . substr($chapter_url, 0, -2));
                    }else{
                        log::warn("manga: $manga_id insert chapter fail");
                    }
                }
            }
            if(isset($chapter_id)){
                $date = date('Y-m-d H:i:s');
                db::query("update manga set latest = '$date' where id = {$manga['id']}");
            }
        }
    }else{ //不存在manga直接添加新的
        $data['url_title'] = encodeUrl($data['title']);
        $data['alphabet'] = getInitial($data['title']);
        $data['source'] = 'mangapark';
        $data['latest'] = date('Y-m-d H:i:s');
        $manga_id = db::insert('manga', $data);
        if($manga_id){
            //取分类
            $genre_selector = "//section/div//table[@class='outer']//table[@class='attr']//tr[7]/td/a";
            $genre = selector::select($page['raw'], $genre_selector);
            if(is_array($genre)){
                foreach($genre as $genre_name){
                    $genre_name = trim($genre_name);
                    db::query("insert into manga_genres (genres_id, manga_id) values((select id from genres where name = '$genre_name'), $manga_id)");
                }
            }else{
                $genre = trim($genre);
                db::query("insert into manga_genres (genres_id, manga_id) values((select id from genres where name = '$genre'), $manga_id)");
            }
            db::query("update manga_genres left join genres on manga_genres.genres_id = genres.id set manga_genres.genres = genres.name, manga_genres.url_genres = genres.url_genres  where manga_genres.genres is null");
            //取章节
            $chapter_selector = "//ul[@class='chapter']/li";
            $chapters = selector::select($page['raw'], $chapter_selector);
            if($chapters){
                $chapters = (array) $chapters;
                foreach($chapters as $v){
                    $chapter_url = selector::select($v, $chapter_url_selector);
                    $chapter_title = selector::select($v, $chapter_title_selector);
                    $chapter_time = selector::select($v, $chapter_time_selector);

                    $insert = mangaChapter($chapter_url, $chapter_title, $chapter_time);
                    $insert['manga_id'] = $manga_id;
                    $chapter_id = db::insert('chapter', $insert);
                    //添加章节URL到队列等待取image
                    if($chapter_id){
                        $phpspider->add_scan_url('http://mangapark.me' . substr($chapter_url, 0, -2));
                    }else{
                        log::warn("manga: $manga_id insert chapter fail");
                    }
                }
            }else{
                log::warn('no chapters : '.$manga_id);
            }
        }
    }
    return $data;
};


$spider->start();
