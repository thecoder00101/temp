<?php
!defined('DEBUG') AND exit('Forbidden');



$tablepre = $db->tablepre;

//给user添加一个字段#avatar_auto
$sql = "ALTER TABLE {$tablepre}user ADD COLUMN avatar_auto CHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  '0' COMMENT  '系统头像'";
db_exec($sql);


?>