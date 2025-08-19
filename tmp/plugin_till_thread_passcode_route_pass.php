<?php

$c = param(1,'');

if ($c == 'ckpass') {
	$tid = param(2, 0);
	$passcode = param('passcode', '');
	$_thread = thread__read($tid);
	if (!$_thread) {
		message(-1, lang('thread_not_exists'));
	}
	$_ps = $_thread['passcode'];
	if ($_ps === $passcode) {
		$_SESSION['passcode_expire_time_for_tid_' . $tid] = time() + 86400;
		message(0, '密码正确，刷新后即可查看。');
	} else {
		message(1, '密码错误！');
	}
}
