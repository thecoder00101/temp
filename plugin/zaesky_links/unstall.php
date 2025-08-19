<?php

/*
	Xiuno BBS 4.0
*/

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;
$sql = "drop table {$tablepre}navlinks";
$r = db_exec($sql);

$r === FALSE AND message(-1, '卸载表失败');
?>