<?php
!defined('DEBUG') AND exit('Forbidden');
$tablepre = $db->tablepre;
$sql = "ALTER TABLE ".$tablepre."thread ADD pinned_comment INT(12) NOT NULL default '0';";
db_exec($sql);
forum_list_cache_delete();
group_list_cache_delete();
?>