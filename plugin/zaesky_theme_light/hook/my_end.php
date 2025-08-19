if(isset($light_config['user_bg_switch']) && $light_config['user_bg_switch'] == 1){
 if($action === 'background'){
	if($method == 'GET'){
		include _include(APP_PATH.'plugin/zaesky_theme_light/view/htm/my_background.htm');
	}else if($method == 'POST'){
    $imgsrc = param('imgsrc');
	if($imgsrc > 0 && $imgsrc <= 9) {
		user_update($uid, array('bgimg'=>$imgsrc));
		message(0, lang('change_bg_success'));
	}else{
		message(0, lang('change_bg_fail'));
	}
}
}
}