<?php

/*
	Xiuno BBS 4.0 插件实例：QQ 登陆插件设置
	admin/plugin-setting-xn_qq_login.htm
*/

!defined('DEBUG') AND exit('Access Denied.');

if($method == 'GET') {
	
	$kv = kv_get('vcode');
	
	
	$input = array();

	$input['vcode_user_create_on'] = form_radio_yes_no('vcode_user_create_on', $kv['vcode_user_create_on']);
	$input['vcode_user_findpw_on'] = form_radio_yes_no('vcode_user_findpw_on', $kv['vcode_user_findpw_on']);
	$input['vcode_thread_create_on'] = form_radio_yes_no('vcode_thread_create_on', $kv['vcode_thread_create_on']);
	$input['vcode_post_create_on'] = form_radio_yes_no('vcode_post_create_on', $kv['vcode_post_create_on']);
	$setting['shee_create_vcode_htm'] = setting_get('shee_create_vcode_htm');
	$setting['shee_create_vcode_2_htm'] = setting_get('shee_create_vcode_2_htm');
		$setting['shee_create_vcode_3_htm'] = setting_get('shee_create_vcode_3_htm');
	// hook plugin_vcode_setting_get_end.htm
	
	include _include(APP_PATH.'plugin/shee_vcode/setting.htm');
	
} else {

	$kv = array();
	$kv['vcode_user_login_on'] = param('vcode_user_login_on');
	$kv['vcode_user_create_on'] = param('vcode_user_create_on');
	$kv['vcode_user_findpw_on'] = param('vcode_user_findpw_on');
	$kv['vcode_thread_create_on'] = param('vcode_thread_create_on');
	$kv['vcode_post_create_on'] = param('vcode_post_create_on');
	setting_set('shee_create_vcode_htm', param('shee_create_vcode_htm', '', FALSE));
	setting_set('shee_create_vcode_2_htm', param('shee_create_vcode_2_htm', '', FALSE));
	setting_set('shee_create_vcode_3_htm', param('shee_create_vcode_3_htm', '', FALSE));
	// hook plugin_vcode_setting_kv_set_before.htm
	kv_set('vcode', $kv);
	
	// hook plugin_vcode_setting_post_end.htm
	message(0, '修改成功');
}
	
?>