<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(3);

if(empty($action)) {
	
	$haowulist = db_find('haowu', array(), array('rank'=>-1), 1, 1000, 'hwid');
	$maxid = db_maxid('haowu', 'hwid');
	
	if($method == 'GET') {
		include _include(APP_PATH.'plugin/wiki_haowu/setting.htm');
	} else {
		$rowidarr = param('rowid', array(0));
		$namearr = param('name', array(''));
		$contentarr = param('content', array(''));
		$sourcearr = param('source', array(''));
		$pricearr = param('price', array('0.00'));
		$scorearr = param('score', array('0.0'));
		$imgarr = param('img', array(''));
		$urlarr = param('url', array(''));
		$reviewarr = param('review', array('')); 
		$rankarr = param('rank', array(0));
		
		$arrlist = array();
		foreach($rowidarr as $k=>$v) {
			if(empty($namearr[$k]) && empty($urlarr[$k]) && empty($rankarr[$k])) continue;
			$arr = array(
				'hwid'=>$k,
				'name'=>$namearr[$k],
				'content'=>$contentarr[$k],
				'source'=>$sourcearr[$k],
				'price'=>$pricearr[$k],
				'score'=>$scorearr[$k],
				'img'=>$imgarr[$k],
				'url'=>$urlarr[$k],
				'review'=>$reviewarr[$k], 
				'rank'=>$rankarr[$k],
			);
			if(!isset($haowulist[$k])) {
				db_create('haowu', $arr);
			} else {
				db_update('haowu', array('hwid'=>$k), $arr);
			}
		}
		
		// 删除
		$deletearr = array_diff_key($haowulist, $rowidarr);
		foreach($deletearr as $k=>$v) {
			db_delete('haowu', array('hwid'=>$k));
		}
		
		message(0, '保存成功');
	}
}
?>