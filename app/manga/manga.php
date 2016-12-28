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
    'tasknum' => 6,
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
        'http://mangapark.me/genre/179'
    ),
    //列表页
    'list_url_regexes' => array(
        "http://mangapark.me/genre/\d+"
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
    'export' => array(
        'type' => 'db',
        'table' => 'manga',
    ),

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
    requests::set_cookies('__cfduid=d2c80354eec831a42a1c173e0fc6001261480584399; _svt=1480669184; st-glsg=1; MarketGidStorage=%7B%220%22%3A%7B%22svspr%22%3A%22http%3A%2F%2Fmangapark.me%2Fmanga%2Ftoritsu-mizushou%22%2C%22svsds%22%3A1%2C%22TejndEEDj%22%3A%22MTQ4MDY3MDQyOTM5NzE1ODk3ODU0MQ%3D%3D%22%7D%2C%22C15897%22%3A%7B%22page%22%3A1%2C%22time%22%3A1480670429402%7D%7D; _lad=3; Hm_lvt_5ee99fa43d3e817978c158dfc8eb72ad=1480584402,1480669182; Hm_lpvt_5ee99fa43d3e817978c158dfc8eb72ad=1480822001; __utma=164524451.623894801.1480584402.1480779753.1480820682.7; __utmc=164524451; __utmz=164524451.1480584402.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __atuvc=10%7C48%2C2%7C49');
};

$spider->on_status_code = function($status_code, $url, $content, $phpspider) {
    if($status_code != '200'){
        return false;
    }
    return $content;
};

$spider->on_scan_page = function($page, $content, $phpspider)
{
    if($page['url'] != 'http://mangapark.me/genre/179'){
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
    $phpspider->add_url('http://mangapark.me/genre/180');
};
$spider->on_list_page = function ($page, $content, $phpspider){
    $page_no = substr($page['url'], strrpos($page['url'], '/')+1);
    if($page_no < 376){
        $page_no = $page_no + 1;
        $phpspider->add_url('http://mangapark.me/genre/'. $page_no);
        log::warn('add list page : '.$page_no);
    }
};

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
    $data['url_title'] = encodeUrl($data['title']);
    $data['alphabet'] = getInitial($data['title']);
    $data['source'] = 'mangapark';
    return $data;
};

$spider->on_insert_db = function ($manga_id, $page, $phpspider){
    //取分类
    $genre_selector = "//section/div//table[@class='outer']//table[@class='attr']//tr[7]/td/a";
    $genre = selector::select($page['raw'], $genre_selector);
    if(!$genre){
        log::warn('no genre : '.$manga_id);
    }else{
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
    }

    //取章节
    $chapter_selector = "//ul[@class='chapter']/li";
    $chapter_url_selector = "//span/a/@href";
    $chapter_title_selector = "//span/text()";
    $chapter_time_selector = "//i";
    $chapters = selector::select($page['raw'], $chapter_selector);
//    var_dump($chapters);exit;
    if($chapters){
        $chapters = (array) $chapters;
        foreach($chapters as $v){
            $chapter_url = selector::select($v, $chapter_url_selector);
            $chapter_title = selector::select($v, $chapter_title_selector);
            $chapter_time = selector::select($v, $chapter_time_selector);

            $insert = mangaChapter($chapter_url, $chapter_title, $chapter_time);
            $insert['manga_id'] = $manga_id;
            $chapter_id = db::insert('chapter', $insert);
            if(!$chapter_id){
                log::warn("manga: $manga_id insert chapter fail");
            }
            //添加章节URL到队列等待取image
            $phpspider->add_scan_url('http://mangapark.me' . substr($chapter_url, 0, -2));
        }

    }else{
        log::warn('no chapters : '.$manga_id);
    }

};

$spider->start();


