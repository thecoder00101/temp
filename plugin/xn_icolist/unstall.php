<?php



!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;
$sql = "DROP TABLE IF EXISTS {$tablepre}icolink;";

$r = db_exec($sql);
$r === FALSE AND message(-1, '卸载友情链接表失败');

?>