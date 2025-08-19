<?php

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

// 删除数据就没有了
db_exec("ALTER TABLE {$tablepre}user DROP COLUMN avatar_auto;");


?>