<?php
exit;
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