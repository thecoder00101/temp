<?php

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

// 创建表
$sql = "CREATE TABLE IF NOT EXISTS {$tablepre}haowu (
  hwid bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(32) NOT NULL DEFAULT '',
  content text NOT NULL,
  source varchar(32) NOT NULL DEFAULT '',
  price decimal(10,2) NOT NULL DEFAULT '0.00',
  score decimal(3,1) NOT NULL DEFAULT '0.0',
  img varchar(255) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  review varchar(255) NOT NULL DEFAULT '',
  rank smallint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (hwid),
  KEY (rank)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4";
$r = db_exec($sql);

// 插入数据（表名应为haowu，字段要全）
$sql = "INSERT INTO {$tablepre}haowu 
(name, content, source, price, score, img, url, review, rank) VALUES 
('修罗百科', '修罗百科,致力于为使用XIUNO开源程序的站长提供免费的服务及主题；插件等资源。', '2024-02-12拼多多入手', '5488', '3.5', 'https://yzrss.com/wp-content/uploads/2024/02/iphone-15-finish-select-202309-6-1inch-blue_pixian_ai.png', 'https://xiuno.wiki/', '', 0)";
$r = db_exec($sql);

?>