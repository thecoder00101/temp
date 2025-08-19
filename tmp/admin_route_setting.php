<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);

include _include(APP_PATH.'model/smtp.func.php');
$smtplist = smtp_init(APP_PATH.'conf/smtp.conf.php');


$menu['setting']['tab'] += array (			
	'extend'=>array('url'=>url('setting-extend'), 'text'=>lang('admin_setting_extend'))
);


$menu['setting']['tab']['sensitive_word'] = array(
'url'=>url('setting-sensitive_word'),
'text'=>lang('sensitive_word_setting'),
);

if($action == 'base') {
	
	
	
	if($method == 'GET') {
		
		
		
		$input = array();
		$input['sitename'] = form_text('sitename', $conf['sitename']);
		$input['sitebrief'] = form_textarea('sitebrief', $conf['sitebrief'], '100%', 100);
		$input['runlevel'] = form_radio('runlevel', array(0=>lang('runlevel_0'), 1=>lang('runlevel_1'), 2=>lang('runlevel_2'), 3=>lang('runlevel_3'), 4=>lang('runlevel_4'), 5=>lang('runlevel_5')), $conf['runlevel']);
		$input['user_create_on'] = form_radio_yes_no('user_create_on', $conf['user_create_on']);
		$input['user_create_email_on'] = form_radio_yes_no('user_create_email_on', $conf['user_create_email_on']);
		$input['user_resetpw_on'] = form_radio_yes_no('user_resetpw_on', $conf['user_resetpw_on']);
		$input['lang'] = form_select('lang', array('zh-cn'=>lang('lang_zh_cn'), 'zh-tw'=>lang('lang_zh_tw'), 'en-us'=>lang('lang_en_us'), 'ru-ru'=>lang('lang_ru_ru'), 'th-th'=>lang('lang_th_th')), $conf['lang']);
		
		$header['title'] = lang('admin_site_setting');
		$header['mobile_title'] =lang('admin_site_setting');
		
		
		
		include _include(ADMIN_PATH.'view/htm/setting_base.htm');
		
	} else {
		
		$sitebrief = param('sitebrief', '', FALSE);
		$sitename = param('sitename', '', FALSE);
		$runlevel = param('runlevel', 0);
		$user_create_on = param('user_create_on', 0);
		$user_create_email_on = param('user_create_email_on', 0);
		$user_resetpw_on = param('user_resetpw_on', 0);
		
		$_lang = param('lang');
		
		
		
		$replace = array();
		$replace['sitename'] = $sitename;
		$replace['sitebrief'] = $sitebrief;
		$replace['runlevel'] = $runlevel;
		$replace['user_create_on'] = $user_create_on;
		$replace['user_create_email_on'] = $user_create_email_on;
		$replace['user_resetpw_on'] = $user_resetpw_on;
		$replace['lang'] = $_lang;
		
		file_replace_var(APP_PATH.'conf/conf.php', $replace);
	
		
		
		message(0, lang('modify_successfully'));
	}

} elseif($action == 'smtp') {

	
	
	if($method == 'GET') {
		
		
		
		$header['title'] = lang('admin_setting_smtp');
		$header['mobile_title'] = lang('admin_setting_smtp');
	
		$smtplist = smtp_find();
		$maxid = smtp_maxid();
		
		
		
		include _include(ADMIN_PATH."view/htm/setting_smtp.htm");
	
	} else {
		
		
		
		$email = param('email', array(''));
		$host = param('host', array(''));
		$port = param('port', array(0));
		$user = param('user', array(''));
		$pass = param('pass', array(''));
		
		$smtplist = array();
		foreach ($email as $k=>$v) {
			$smtplist[$k] = array(
				'email'=>$email[$k],
				'host'=>$host[$k],
				'port'=>$port[$k],
				'user'=>$user[$k],
				'pass'=>$pass[$k],
			);
		}
		$r = file_put_contents_try(APP_PATH.'conf/smtp.conf.php', "<?php\r\nreturn ".var_export($smtplist,true).";\r\n?>");
		!$r AND message(-1, lang('conf/smtp.conf.php', array('file'=>'conf/smtp.conf.php')));
		
		
		
		message(0, lang('save_successfully'));
	}
}


if($action == 'sendmail') {
	if($method == 'POST') {
		$target_email = param('target_email');
		empty($target_email) AND message('target_email', lang('email_is_empty'));
		$email_subject = param('email_subject');
		empty($email_subject) AND message('email_subject', lang('email_subject_is_empty'));
		$email_content = param('email_content');
		empty($email_content) AND message('email_content', lang('email_content_is_empty'));
		!is_email($target_email, $err) AND message('target_email', $err);
		
		include _include(XIUNOPHP_PATH.'xn_send_mail.func.php');
		$smtplist = include _include(APP_PATH.'conf/smtp.conf.php');
		$n = array_rand($smtplist);
		$smtp = $smtplist[$n];
		$r = xn_send_mail($smtp, $conf['sitename'], $target_email, $email_subject, $email_content);
		
		if($r === TRUE) {
			message(0, lang('send_successfully'));
		} else {
			//xn_log($errstr, 'send_mail_error');
			message(-1, $errstr);
		}
	}
}
elseif($action == 'extend') {
		
	if($method == 'GET') {

	    $input = array();
		$input['runlevel_reason'] = form_textarea('runlevel_reason', $conf['runlevel_reason'], '100%', 100);
		$input['url_rewrite_on'] = form_radio_yes_no('url_rewrite_on', $conf['url_rewrite_on']); 
		$input['cdn_on'] = form_radio_yes_no('cdn_on', $conf['cdn_on']); 
		$input['admin_bind_ip'] = form_radio_yes_no('admin_bind_ip',$conf['admin_bind_ip']); 
		$input['pagesize'] = form_text('pagesize', $conf['pagesize'], 100); 
		$input['postlist_pagesize'] = form_text('postlist_pagesize', $conf['postlist_pagesize'], 100); 
		$input['site_keywords'] = form_text('site_keywords', empty($conf['site_keywords'])?'':$conf['site_keywords']); 
		$input['user_create_io'] = form_radio_yes_no('user_create_io', empty($conf['user_create_io'])? 0 :$conf['user_create_io']);

		$input['attach_maxsize'] = form_text('attach_maxsize', empty($conf['attach_maxsize'])? 20480000 :$conf['attach_maxsize']);

	    $header['title'] = lang('admin_setting_extend');
		$header['mobile_title'] = lang('admin_setting_extend');
		include _include(APP_PATH.'plugin/huux_set/view/htm/setting_extend.htm');

	} else {

		$runlevel_reason = param('runlevel_reason', '', FALSE);
		$url_rewrite_on = param('url_rewrite_on', 0); 
		$cdn_on = param('cdn_on', 0); 
		$admin_bind_ip = param('admin_bind_ip', 0); 
		$pagesize = param('pagesize', 0); 
		$postlist_pagesize = param('postlist_pagesize', 0);  
		$site_keywords = param('site_keywords', '', FALSE); 
		$user_create_io = param('user_create_io', 0); 
		$attach_maxsize = param('attach_maxsize', 0); 

		$replace = array();
		$replace['runlevel_reason'] = $runlevel_reason;
		$replace['url_rewrite_on'] = $url_rewrite_on;
		$replace['cdn_on'] = $cdn_on;
		$replace['admin_bind_ip'] = $admin_bind_ip;
		$replace['pagesize'] = $pagesize;
		$replace['postlist_pagesize'] = $postlist_pagesize; 
		$replace['site_keywords'] = $site_keywords;
		$replace['user_create_io'] = $user_create_io;
		$replace['attach_maxsize'] = $attach_maxsize;

		file_replace_var(APP_PATH.'conf/conf.php', $replace);

	    message(0, lang('save_successfully'));

	}

}


elseif($action == 'sensitive_word') {
	
	
	
	if($method == 'GET') {
		
		
		
		$sensitive_words = kv_get('qt_sensitive_words');
		if(!$sensitive_words) {
			$sensitive_words = array('username_sensitive_words'=>array(), 'post_sensitive_words'=>array());
		}
		$sensitive_words['username_sensitive_words'] = implode(' ', $sensitive_words['username_sensitive_words']);
		$sensitive_words['username_sensitive_words'] = htmlspecialchars($sensitive_words['username_sensitive_words']);
		$sensitive_words['post_sensitive_words'] = implode(' ', $sensitive_words['post_sensitive_words']);
		$sensitive_words['post_sensitive_words'] = htmlspecialchars($sensitive_words['post_sensitive_words']);
		$input = array();
		$input['username_sensitive_words'] = form_textarea('username_sensitive_words', $sensitive_words['username_sensitive_words'], '100%', 200);
		$input['post_sensitive_words'] = form_textarea('post_sensitive_words', $sensitive_words['post_sensitive_words'], '100%', 300);
		
		$header['title'] = lang('sensitive_word_setting');
		$header['mobile_title'] =lang('sensitive_word_setting');
		
		
		
		include _include(APP_PATH.'plugin/qt_sensitive_word/view/htm/setting_sensitive_word.htm');
		
	} else {
		
		$username_sensitive_words = param('username_sensitive_words', '', FALSE);
		$post_sensitive_words = param('post_sensitive_words', '', FALSE);
		
		
		$username_sensitive_words = str_replace("　 ", ' ', $username_sensitive_words);
		$username_sensitive_words = preg_replace('#\s+#is', ' ', $username_sensitive_words);
		$username_sensitive_words = explode(' ', $username_sensitive_words);
		$post_sensitive_words = str_replace("　 ", ' ', $post_sensitive_words);
		$post_sensitive_words = preg_replace('#\s+#is', ' ', $post_sensitive_words);
		$post_sensitive_words = explode(' ', $post_sensitive_words);
		kv_set('qt_sensitive_words', array('username_sensitive_words'=>$username_sensitive_words, 'post_sensitive_words'=>$post_sensitive_words));
		
		
		message(0, lang('modify_successfully'));
	}

}

?>