<?php

/*
* Copyright (C) 2015 xiuno.com
*/

!defined('DEBUG') AND exit('Access Denied.');



$page = param(1, 1);
$order = $conf['order_default'];
$order != 'tid' AND $order = 'lastpid';
$pagesize = $conf['pagesize'];
$active = 'default';

// 从默认的地方读取主题列表
$thread_list_from_default = 1;

$digest = param(2, 0);
if($digest == 1) {
	$thread_list_from_default = 0;
	$active = 'digest';
	$digests = thread_digest_count($fid);
	
	$pagination = pagination(url("$route-{page}-1"), $digests, $page, $pagesize);
	
	$threadlist = thread_digest_find_by_fid($fid, $page, $pagesize);
}if (isset($light_config['new_threadlist']) && $light_config['new_threadlist'] == 1) {
$digest = param(2,0);
if($digest == 5) {
	$thread_list_from_default = 0;
	$active = 'newpost';
	$order = 'tid';
	$fids = arrlist_values($forumlist_show, 'fid');
	$threads = arrlist_sum($forumlist_show, 'threads');
	$pagination = pagination(url("index-{page}-5"), $threads, $page, $pagesize);
	$threadlist = thread_find_by_fids($fids, $page, $pagesize, $order, $threads);
}
 }
 
if($thread_list_from_default) {
	$fids = arrlist_values($forumlist_show, 'fid');
	$threads = arrlist_sum($forumlist_show, 'threads');
	$pagination = pagination(url("$route-{page}"), $threads, $page, $pagesize);
	
	
	$threadlist = thread_find_by_fids($fids, $page, $pagesize, $order, $threads);
}

// 查找置顶帖
if($order == $conf['order_default'] && $page == 1) {
	$toplist3 = thread_top_find(0);
	$threadlist = $toplist3 + $threadlist;
}

// 过滤没有权限访问的主题 / filter no permission thread
thread_list_access_filter($threadlist, $gid);

// SEO
$header['title'] = $conf['sitename']; 				// site title
$header['keywords'] = ''; 					// site keyword
$header['description'] = $conf['sitebrief']; 			// site description
$_SESSION['fid'] = 0;



$header['keywords'] = empty($conf['site_keywords']) ? '': $conf['site_keywords'];



include _include(APP_PATH.'view/htm/index.htm');

?>