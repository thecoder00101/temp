<?php

!defined('DEBUG') AND exit('Access Denied.');

if($method == 'GET') {
	$setting['footer_footer_left_end_htm'] = setting_get('footer_footer_left_end_htm');
	$setting['footer_footer_right_end_htm'] = setting_get('footer_footer_right_end_htm');
	include _include(APP_PATH.'plugin/xn_fonts/setting.htm');
	
} else {

	setting_set('footer_footer_left_end_htm', param('footer_footer_left_end_htm', '', FALSE));
	setting_set('footer_footer_right_end_htm', param('footer_footer_right_end_htm', '', FALSE));
	message(0, '修改成功');
}
	
?>