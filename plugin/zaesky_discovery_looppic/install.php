<?php


!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

$sql = "CREATE TABLE IF NOT EXISTS {$tablepre}swiperpic (
  slideid bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  name char(32) NOT NULL DEFAULT '',
  url char(255) NOT NULL DEFAULT '',
  slidepic varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (slideid)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
";
$r = db_exec($sql);
$sql = "INSERT INTO {$tablepre}swiperpic SET  name='这是一张图片示例1', url='https://www.noteweb.top/', slidepic='https://api.likepoems.com/img/aliyun/bing'";
$r = db_exec($sql);
?>