<?php
!defined('DEBUG') AND exit('Forbidden');

/**
 * @var bool $DELETE_DATA_WHEN_UNINST  卸载插件时删除插件数据？
 */
$DELETE_DATA_WHEN_UNINST = true;

if($DELETE_DATA_WHEN_UNINST){
    $tablepre = $db->tablepre;
    $sql = "ALTER TABLE ".$tablepre."thread DROP COLUMN pinned_comment;";
    db_exec($sql);
}
forum_list_cache_delete();
group_list_cache_delete();
?>