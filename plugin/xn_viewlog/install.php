<?php
!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

$sql = "CREATE TABLE IF NOT EXISTS {$tablepre}viewlog (
    uid int(11) unsigned NOT NULL DEFAULT 0, 
    username varchar(150) NOT NULL DEFAULT '',
	tid int(11) unsigned NOT NULL DEFAULT 0,
	dateline int(10) unsigned NOT NULL DEFAULT 0,
	key(uid, tid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

    $viewlog = array();
    $viewlog['title'] = '看过的人';
    $viewlog['maxnum'] = 10;
    $viewlog['days'] = 0;
    kv_set('xn_viewlog', $viewlog);
