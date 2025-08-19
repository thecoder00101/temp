<?php
/*
	
*/
!defined('DEBUG') AND exit('Access Denied.');
if($method == 'GET') {
    //展示
	$setting['xn_accesscount'] = setting_get('xn_accesscount');
	include _include(APP_PATH.'plugin/xn_accesscount/setting.htm');
} else {
    //保存
    $data['count'] = param('xn_accesscount_count', '', FALSE);
    $data['words'] = param('xn_accesscount_words', '', FALSE);
	setting_set('xn_accesscount', $data);
	message(0, '修改成功');
}
