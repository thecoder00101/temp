<?php

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;
$sql = "DROP TABLE IF EXISTS {$tablepre}haowu;";

$r = db_exec($sql);
$r === FALSE AND message(-1, '卸载好物推荐失败');

?>