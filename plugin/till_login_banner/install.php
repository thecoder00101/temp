<?php
!defined('DEBUG') AND exit('Forbidden');

$setting = setting_get('till_login_banner_setting');
if(empty($setting)) {
	$setting = array(
		'allow_close' => false,
		'hint_text' => '逛了这许久，何不进去瞧瞧？',
		'theme' => 'primary',
	);
	setting_set('till_login_banner_setting', $setting);
}

?>