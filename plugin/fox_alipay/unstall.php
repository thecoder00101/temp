<?php
/*

 */
!defined('DEBUG') AND exit('Forbidden');

// 如果要彻底删除所有数据，请去掉下面的注释符
$tablepre = $db->tablepre;
$sql = "DROP TABLE IF EXISTS `{$tablepre}user_foxpay`;";
db_exec($sql);

kv_cache_delete('fox_alipay');
?>