<?php
exit;

if ($action == 'post_like') {

	$header['title'] = lang('haya_post_like') . " - " . $conf['sitename'];

	$haya_post_like_config = setting_get('haya_post_like');
	
	if ($method == 'POST') {

		$tid = param('tid');

		$thread = thread_read($tid);
		empty($thread) AND message(0, lang('thread_not_exists'));
		$haya_check_post_like = haya_post_like_find_by_uid_and_tid($uid, $tid);
		
		$haya_post_like_user_post_like_count = isset($haya_post_like_config['user_post_like_count']) ? intval($haya_post_like_config['user_post_like_count']) : 20;
		
		$haya_post_like_users = haya_post_like_find_by_tid($tid, $haya_post_like_user_post_like_count);
		
		ob_start();
		include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_post_like_users.htm');	
		$haya_favorite_user_html = ob_get_clean();

		message(1, $haya_post_like_user_html);
	}
	
	message(1, lang('haya_post_like_error'));

} else


?>