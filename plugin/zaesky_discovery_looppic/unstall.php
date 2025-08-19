<?php

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;
$sql = "DROP TABLE IF EXISTS {$tablepre}swiperpic;";

$r = db_exec($sql);
$r === FALSE AND message(-1, '卸载图片轮播表失败');

?>