<?php

/*
	Xiuno BBS 4.0 知乎蓝简约主题 知乎导航栏2020
*/

!defined('DEBUG') AND exit('Forbidden');


$kv = kv_get('zz_iqismart_com_autolink');


try{

    $tablepre = $db->tablepre;
  $sql = "CREATE TABLE IF NOT EXISTS {$tablepre}autolink (
  	id int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
    `create_time` bigint(11) unsigned DEFAULT NULL COMMENT '创建时间',
    `siteTitle` varchar(255)  DEFAULT NULL COMMENT '站点名称',
    `siteUrl` varchar(255)  DEFAULT NULL COMMENT '站点url',
    `siteDesc` varchar(255)  DEFAULT NULL COMMENT '站点描述',
    `status` int(11)  DEFAULT 0 COMMENT '状态：0待验证 1正常 -1验证失败',
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
  $r = db_exec($sql);
  $r === FALSE AND message(-1, '创建自动友链表结构失败');

  db_create('autolink', array('uid'=>0, 'siteTitle'=>'一起smart','siteUrl'=>'https://www.iqismart.com','siteDesc'=>'开源 分享 互助 一起开发 一起成长 一起SMART
','create_time'=>time(),'status'=>1));

}catch(Exception $e){}

$kv = kv_cache_get('iqismart_com_nav');
if(!$kv) {
	//$kv = array('type'=>'golds', 'count'=>1);
//	kv_cache_set('iqismart_com_v', $kv);
}

?>


?>