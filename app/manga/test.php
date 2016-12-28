<?php

ini_set("memory_limit", "1024M");
require __DIR__ . '/../../phpspider/core/init.php';

// $counts = 0;
// for ($i=0; $i < 100; $i++) {
// 	$count = db::get_one('select count(*) count from img_'.$i);
// 	$counts = $counts + $count['count'];
// }
// var_dump($counts);
// exit;
$manga = [];
for ($i=0; $i < 22568; $i++) {
	$chapter = db::get_one('select count(*) count from chapter where manga_id = '.$i);
	if($chapter['count']){
		$manga[] = $i;
	}
}
