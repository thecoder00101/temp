<?php

/*
	Xiuno BBS 4.0 插件实例：广告插件设置
	admin/plugin-setting-till_login_banner.htm
*/

!defined('DEBUG') AND exit('Access Denied.');

$setting = setting_get('till_login_banner_setting');

if($method == 'GET') {
	
	$input = array();
	$input['allow_close'] = form_radio_yes_no('allow_close', $setting['allow_close']);
	$input['hint_text'] = form_textarea('hint_text', $setting['hint_text']);
	$input['theme'] = form_select('theme', array('light'=>'浅色', 'dark' => '深色' ,'primary'=>'蓝色（主色调）', 'info' => '青绿色（提示）', 'warning' => '橙色（警告）'), $setting['theme']);
	
	include _include(APP_PATH.'plugin/till_login_banner/setting.htm');
	
} else {

	$setting['allow_close'] = param('allow_close', '', FALSE);
	$setting['hint_text'] = param('hint_text', '', FALSE);
	$setting['theme'] = param('theme', '', FALSE);
	
	setting_set('till_login_banner_setting', $setting);
	
	message(0, '修改成功');
}
	
?>