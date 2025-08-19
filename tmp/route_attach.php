<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);



if(empty($action) || $action == 'create') {
	
	$user = user_read($uid);
	user_login_check();
	
	$width = param('width', 0);
	$height = param('height', 0);
	$is_image = param('is_image', 0);
	$name = param('name');
	$data = param('data', '', FALSE, FALSE);
	$data = param_base64('data');
	
		!ipaccess_check($longip, 'attachs') AND message(-1, '您的 IP 今日附件数达到上限。');
	!ipaccess_check($longip, 'attachsizes') AND message(-1, '您的 IP 今日附件总大小达到上限。');
	
	// 允许的文件后缀名
	//$types = include _include(APP_PATH.'conf/attach.conf.php');
	//$allowtypes = $types['all'];
	
	empty($group['allowattach']) AND $gid != 1 AND message(-1, '您无权上传');
	
	empty($data) AND message(-1, lang('data_is_empty'));
	//$data = base64_decode_file_data($data);
	$size = strlen($data);
	$size > 20480000 AND message(-1, lang('filesize_too_large', array('maxsize'=>'20M', 'size'=>$size)));
	
	// 111.php.shtmll 
	$ext = file_ext($name, 7);
	$filetypes = include APP_PATH.'conf/attach.conf.php';
	!in_array($ext, $filetypes['all']) AND $ext = '_'.$ext;
	
	$tmpanme = $uid.'_'.xn_rand(15).'.'.$ext;
	$tmpfile = $conf['upload_path'].'tmp/'.$tmpanme;
	$tmpurl = $conf['upload_url'].'tmp/'.$tmpanme;
	
	$filetype = attach_type($name, $filetypes);
	
	

    $attach_maxsize = empty($conf['attach_maxsize']) ? 20971520 : $conf['attach_maxsize'];
	$size > $attach_maxsize AND message(-1, lang('filesize_too_large', array('maxsize'=> round($attach_maxsize / 1048576 * 100) / 100 .'Mb', 'size'=>round($size / 1048576 * 100) / 100 .'Mb')));


	
	file_put_contents($tmpfile, $data) OR message(-1, lang('write_to_file_failed'));
	
	// 保存到 session，发帖成功以后，关联到帖子。
	// save attach information to session, associate to post after create thread.

	// 抛弃之前的 $_SESSION 数据，重新启动 session，降低 session 并发写入的问题
	// Discard the previous $_SESSION data, restart the session, reduce the problem of concurrent session write
	sess_restart();
	
	empty($_SESSION['tmp_files']) AND $_SESSION['tmp_files'] = array();
	$n = count($_SESSION['tmp_files']);
	$filesize = filesize($tmpfile);
	$attach = array(
		'url'=>$tmpurl, 
		'path'=>$tmpfile, 
		'orgfilename'=>$name, 
		'filetype'=>$filetype, 
		'filesize'=>$filesize, 
		'width'=>$width, 
		'height'=>$height, 
		'isimage'=>$is_image, 
		'downloads'=>0, 
		'aid'=>'_'.$n
	);
	$_SESSION['tmp_files'][$n] = $attach;
	
	unset($attach['path']);
	
			ipaccess_inc($longip, 'attachs');
		ipaccess_inc($longip, 'attachsizes', $filesize);
sg_watermark($tmpfile, $ext);

	
	message(0, $attach);

} elseif($action == 'delete') {
	
	$user = user_read($uid);
	user_login_check();

	$aid = param(2);
	
	
	
	// 临时的文件 id / temp attach id : _0 _1 _2 _3 ...
	if(substr($aid, 0, 1) == '_') {
		$key = intval(substr($aid, 1));
		$tmp_files = _SESSION('tmp_files');
		!isset($tmp_files[$key]) AND message(-1, lang('item_not_exists', array('item'=>$key)));
		$attach = $tmp_files[$key];
		!is_file($attach['path']) AND message(-1, lang('file_not_exists'));
		unlink($attach['path']);
		unset($_SESSION['tmp_files'][$key]);
	} else {
		$aid = intval($aid);
		$attach = attach_read($aid);
		empty($attach) AND message(-1, lang('attach_not_exists'));
		
		$thread = thread_read($attach['tid']);
		empty($thread) AND message(-1, lang('thread_not_exists'));
		$fid = $thread['fid'];
		
		$allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
		$attach['uid'] != $uid AND !$allowdelete AND message(0, lang('insufficient_privilege'));
		
		$r = attach_delete($aid);
		$r ===  FALSE AND message(-1, lang('delete_failed'));
	}
	
	
	
	message(0, 'delete_successfully');
	
} elseif($action == 'download') {
	
	
	
	// 判断权限
	$aid = param(2, 0);
	$attach = attach_read($aid);
	empty($attach) AND message(-1, lang('attach_not_exists'));
	$tid = $attach['tid'];
	$thread = thread_read($tid);
	$fid = $thread['fid'];
	$allowdown = forum_access_user($fid, $gid, 'allowdown');
	empty($allowdown) AND message(-1, lang('insufficient_privilege_to_download'));	
	
	$attachpath = $conf['upload_path'].'attach/'.$attach['filename'];
	$attachurl = $conf['upload_url'].'attach/'.$attach['filename'];
	!is_file($attachpath)AND message(-1, lang('attach_not_exists'));
	
	$type = 'php';
	
	if($thread['content_buy']) {

    $content_pay = db_find_one('paylist', array('tid' => $tid, 'uid' => $uid, 'type' => 1));
    if ((!$content_pay) && ($thread['uid'] != $uid)) { message(-1, lang('cannot_down')); die(); }

}
$set = setting_get('tt_credits');
$credits = $set['down_exp'];
$credits_op = $credits > 0 ? '+' : '';
$golds = $set['down_gold'];
$golds_op = $golds > 0 ? '+' : '';
$rmbs = $set['down_rmb'];
$rmbs_op = $rmbs > 0 ? '+' : '';
$user = user_read($uid);
if ((empty($user)) && ($credits != 0 || $golds != 0 || $rmbs != 0)) {
    message(-1, lang('insufficient_privilege_to_download'));
}

if (!empty($user)) {
    if (($credits < 0 && ($user['credits'] + $credits < 0)) || ($golds < 0 && ($user['golds'] + $golds < 0)) || ($rmbs < 0 && ($user['rmbs'] + $rmbs < 0))) {
        message(-1, jump(lang('credit_no_enough'),url('my-credits'),2));
        die(); }
    $uid AND user_update($uid, array('credits+' => $credits, 'golds+' => $golds, 'rmbs+' => $rmbs));
    $uid AND user_update_group($uid);
}

	
	// php 输出
	if($type == 'php') {

		attach_update($aid, array('downloads+'=>1));
		
		$filesize = $attach['filesize'];
		if(stripos($_SERVER["HTTP_USER_AGENT"], 'MSIE') !== FALSE || stripos($_SERVER["HTTP_USER_AGENT"], 'Edge') !== FALSE || stripos($_SERVER["HTTP_USER_AGENT"], 'Trident') !== FALSE) {
			$attach['orgfilename'] = urlencode($attach['orgfilename']);
			$attach['orgfilename'] = str_replace("+", "%20", $attach['orgfilename']);
		}
		$timefmt = date('D, d M Y H:i:s', $time).' GMT';
		header('Date: '.$timefmt);
		header('Last-Modified: '.$timefmt);
		header('Expires: '.$timefmt);
	       // header('Cache-control: max-age=0, must-revalidate, post-check=0, pre-check=0');
		header('Cache-control: max-age=86400');
		header('Content-Transfer-Encoding: binary');
		header("Pragma: public");
		header('Content-Disposition: attachment; filename="'.$attach['orgfilename'].'"');
		header('Content-Type: application/octet-stream');
		//header("Content-Type: application/force-download");	// 后面的会覆盖前面
		
		
		
		readfile($attachpath);
		
		/*if($attach['filetype'] == 'image') {
			// ie6 下会解析图片内容！
			//header('Content-Disposition: inline; filename='.$attach['orgfilename']);
			//header('Content-Type: image/pjpeg');
		} else {
			header('Content-Disposition: attachment; filename='.$attach['orgfilename']);
			header('Content-Type: application/octet-stream');
		}*/
		exit;
	} else {
		
		
		
		http_location($attachurl);
	}
}



?>