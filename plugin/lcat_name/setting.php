<?php

!defined('DEBUG') AND exit('Access Denied.');

if($method == 'GET') {
 $setting['threadss'] = setting_get('threadss');
 $setting['postss'] = setting_get('postss');
 
 include _include(APP_PATH.'plugin/lcat_name/setting.htm');
 
} else {
setting_set('threadss', param('threadss','', FALSE));
setting_set('postss', param('postss','', FALSE));

 message(0, '已成功设置');
}
 
?>