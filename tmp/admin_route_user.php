<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);



if(empty($action) || $action == 'list') {

	$header['title'] = lang('user_admin');
	$header['mobile_title'] = lang('user_admin');
		
	$pagesize = 20;
	$srchtype = param(2);
	$keyword  = trim(xn_urldecode(param(3)));
	$page     = param(4, 1);

	
	
	$cond = array();
	$allowtype = array('uid', 'username', 'email', 'gid', 'create_ip');
	
	
	
	if($keyword) {
		!in_array($srchtype, $allowtype) AND $srchtype = 'uid';
		$cond[$srchtype] = $srchtype == 'create_ip' ? ip2long($keyword) : $keyword; 
	}

	
	$n = user_count($cond);
	$userlist = user_find($cond, array('uid'=>-1), $page, $pagesize);
	$pagination = pagination(url("user-list-$srchtype-".urlencode($keyword).'-{page}'), $n, $page, $pagesize);
	$pager = pager(url("user-list-$srchtype-".urlencode($keyword).'-{page}'), $n, $page, $pagesize);

	foreach ($userlist as &$_user) {
		$_user['group'] = array_value($grouplist, $_user['gid'], '');
	}

	
function zharrip($ipip){
$ipip[0]!='中国' && $ip=$ipip[0];

 if($ipip[1]!=''){
   $ip=!isset($ip) ? '':$ip.'-';
   $ip=$ip.$ipip[1];
   $ipip[2]!='' && $ipip[2]!=$ipip[1] &&  $ip=$ip.'-'.$ipip[2];
 }
 return $ip;
}
include APP_PATH.'/plugin/xu_ipinf/IP4datx.class.php';
$ipfind=new IP;
	foreach ($userlist as &$_user) {
	$ipip=$ipfind->find($_user['create_ip_fmt']);
	$_user['create_ip_fmt']=$_user['create_ip_fmt'].'('.zharrip($ipip).')';
	}
//message(0,);
	
	include _include(ADMIN_PATH."view/htm/user_list.htm");

} elseif($action == 'create') {

	
	
	if($method == 'GET') {

		
		
		$header['title'] = lang('admin_user_create');
		$header['mobile_title'] = lang('admin_user_create');
		
		$input['email'] = form_text('email', '');
		$input['username'] = form_text('username','');
		$input['password'] = form_password('password', '');
		$grouparr = arrlist_key_values($grouplist, 'gid', 'name');
		$input['_gid'] = form_select('_gid', $grouparr, 0);
		
		
		
		include _include(ADMIN_PATH."view/htm/user_create.htm");

	} elseif ($method == 'POST') {

		$email = param('email');
		$username = param('username');
		$password = param('password');
		$_gid = param('_gid');
		
		
		
		empty($email) AND message('email', lang('please_input_email'));
		$email AND !is_email($email, $err) AND message('email', $err);
		$username AND !is_username($username, $err) AND message('username', $err);

		$_user = user_read_by_email($email);
		$_user AND message('email', lang('email_is_in_use'));

		$_user = user_read_by_username($username);
		$_user AND message('username', lang('user_already_exists'));

		$salt = xn_rand(16);
		$r = user_create(array(
			'username'=>$username,
			'password'=>md5(md5($password).$salt),
			'salt'=>$salt,
			'gid'=>$_gid,
			'email'=>$email,
			'create_ip'=>ip2long(ip()),
			'create_date'=>$time
		));
		$r === FALSE AND message(-1, lang('create_failed'));
		
		
		
		message(0, lang('create_successfully'));

	}

} elseif($action == 'update') {

	$_uid = param(2, 0);
	
	
	
	if($method == 'GET') {

		
		
		$header['title'] = lang('user_edit');
		$header['mobile_title'] = lang('user_edit');
		
		$_user = user_read($_uid);
		
		$input['email'] = form_text('email', $_user['email']);
		$input['username'] = form_text('username', $_user['username']);
		$input['password'] = form_password('password', '');
		$grouparr = arrlist_key_values($grouplist, 'gid', 'name');
		$input['_gid'] = form_select('_gid', $grouparr, $_user['gid']);

		$input['signature'] = form_textarea('my_signature', $_user['signature'], '100%', 150);$input['credits'] = form_text('credits', $_user['credits']);
$input['golds'] = form_text('golds', $_user['golds']);
$input['rmbs'] = form_text('rmbs', $_user['rmbs']);
		
		include _include(ADMIN_PATH."view/htm/user_update.htm");

	} elseif($method == 'POST') {

		$email = param('email');
		$username = param('username');
		$password = param('password');
		$_gid = param('_gid');
		
		$signature = param('my_signature', '', $htmlspecialchars = FALSE);
$signature = strip_tags($signature,"<b>,<br>,<a>,<img>,<span>");
include _include(APP_PATH.'plugin/art_signature/model/xss.php');
$signature = remove_xss($signature);
$signature = htmlspecialchars($signature);$credits=param('credits');$golds=param('golds'); $rmbs=param('rmbs');
		
		$old = user_read($_uid);
		empty($old) AND message('username', lang('uid_not_exists'));
		
		$email AND !is_email($email, $err) AND message(2, $err);
		if($email AND $old['email'] != $email) {
			$_user = user_read_by_email($email);
			$_user AND $_user['uid'] != $_uid AND message('email', lang('email_already_exists'));
		}
		if($username AND $old['username'] != $username) {
			$_user = user_read_by_username($username);
			$_user AND $_user['uid'] != $_uid AND message('username', lang('user_already_exists'));
		}
		
		$arr = array();
		$arr['email'] = $email;
		$arr['username'] = $username;
		$arr['gid'] = $_gid;
		
		if($password) {
			$salt = xn_rand(16);
			$arr['password'] = md5(md5($password).$salt);
			$arr['salt'] = $salt;
		}
		
		$arr['signature']=$signature;$arr['credits']=$credits;
$arr['golds']=$golds;
$arr['rmbs']=$rmbs;
		
		// 仅仅更新发生变化的部分 / only update changed field
		$update = array_diff_value($arr, $old);
		empty($update) AND message(-1, lang('data_not_changed'));

		$r = user_update($_uid, $update);
		$r === FALSE AND message(-1, lang('update_failed'));
		
		
    if($old['gid'] == 7 && ($_gid != 7)){
        fox_prison_update($_uid, $_gid);
    }
    if($_gid == 7 && ($old['gid'] > 7)){
        $message = param('message');
        db_create('fox_prison', array('uid'=>$_uid, 'aid'=>$uid, 'time'=>$time, 'endtime'=>strtotime("+15 year", $time), 'uip'=>$longip, 'message'=>$message));
    }
if($_gid>=100){
    user_update_group($_uid);
}
		
		message(0, lang('update_successfully'));
	}

} elseif($action == 'delete') {

	if($method != 'POST') message(-1, 'Method Error.');

	$_uid = param('uid', 0);
	
	
	
	$_user = user_read($_uid);
	empty($_user) AND message(-1, lang('user_not_exists'));
	($_user['gid'] == 1) AND message(-1, 'admin_cant_be_deleted');

	$r = user_delete($_uid);
	$r === FALSE AND message(-1, lang('delete_failed'));
	
	
	
	message(0, lang('delete_successfully'));
	
}



?>