<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(3);

if(empty($action)) {
	
	$slidelist = db_find('swiperpic', array(), array('slideid'=>1), 1, 1000, 'slideid');
	$maxid = db_maxid('swiperpic', 'slideid');
	
	if($method == 'GET') {
		
		include _include(APP_PATH.'plugin/zaesky_discovery_looppic/setting.htm');
		
	} else {
		
		$rowidarr = param('rowid', array(0));
		$namearr = param('name', array(''));
		$urlarr = param('url', array(''));
		$slidepicarr = param('slidepic', array(''));
		
		$arrlist = array();
		foreach($rowidarr as $k=>$v) {
			if(empty($namearr[$k]) && empty($urlarr[$k]) && empty($slidepicarr[$k])) continue;
			$arr = array(
				'slideid'=>$k,
				'name'=>$namearr[$k],
				'url'=>$urlarr[$k],
				'slidepic'=>$slidepicarr[$k],
			);
			if(!isset($slidelist[$k])) {
				db_create('swiperpic', $arr);
			} else {
				db_update('swiperpic', array('slideid'=>$k), $arr);
			}
		}
		
		// 删除
		$deletearr = array_diff_key($slidelist, $rowidarr);
		foreach($deletearr as $k=>$v) {
			db_delete('swiperpic', array('slideid'=>$k));
		}
		
		message(0, '保存成功');
	}
}
?>