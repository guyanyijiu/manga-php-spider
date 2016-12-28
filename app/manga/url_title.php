<?php
/**
 * Created by PhpStorm.
 * User: liuchao
 * Date: 2016/11/28
 * Time: 上午10:28
 */

ini_set("memory_limit", "1024M");
require __DIR__ . '/../../phpspider/config/inc_config.php';
require __DIR__ . '/../../phpspider/core/db.php';
require __DIR__ . '/../../phpspider/common/functions.php';

$sql = "select id,title from manga";
$data = db::query($sql);
foreach ($data as $v){
    $url_title = encodeUrl($v['title']);
    db::query("update manga set url_title = '$url_title' where id = {$v['id']}");
}