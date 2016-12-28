<?php

ini_set("memory_limit", "1024M");
require __DIR__ . '/../../phpspider/core/init.php';


// for ($i=0; $i < 100; $i++) {
// $sql = <<<EOT
// CREATE TABLE `img_$i` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `chapter_id` int(11) DEFAULT NULL,
//   `img` varchar(255) DEFAULT NULL,
//   `created` datetime DEFAULT CURRENT_TIMESTAMP,
//   PRIMARY KEY (`id`),
//   KEY `chapter_id` (`chapter_id`)
// ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
// EOT;
// 	db::query($sql);
// }
//
for ($i=0; $i < 100; $i++) {
	db::query("insert into img_$i (chapter_id, img) select chapter_id,img from img where mod(chapter_id, 100) = $i;");
}