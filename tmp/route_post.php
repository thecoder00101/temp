<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);

user_login_check();


if($action == 'pin_comment' && $method=='POST'){
    if(empty($uid)||empty($user)) { 
        message(-1,'请登录后再操作！');
        die();
    }
    $_pid = param(2,0);
    if(empty($_pid)){
        message(-1,'请指定评论');
        die();
    }
    $_post = post_read($_pid);
    $_post_uid = $_post['uid'];
    if(empty($_post)){
        message(-1,'Bad Request');
        die();
    }
    $_thread = thread_read($_post['tid']);
    if(empty($_thread)){
        message(2,'帖子不存在');
        die();
    }
    $_thread_uid = intval($_thread['uid']);

    if($uid == $_thread_uid){
        db_update('thread',array('tid'=>$_post['tid']),array('pinned_comment'=>$_pid));
    } else {
        message(3,'Bad Request');
        die();
    }
    unset($_post,$_post_uid,$_thread,$_thread_uid );
    message(0,'置顶成功！');
}

if($action == 'unpin_comment' && $method=='POST'){
    if(empty($uid)||empty($user)) { 
        message(-1,'请登录后再操作！');
        die();
    }
    $_tid = param(2,0);
    if(empty($_tid)){
        message(-1,'请指定帖子');
        die();
    }
    $_thread = thread_read($_tid);
    if(empty($_thread)){
        message(2,'帖子不存在');
        die();
    }
    $_thread_uid = intval($_thread['uid']);
    if($uid == $_thread_uid){
        db_update('thread',array('tid'=>$_tid),array('pinned_comment'=> 0 ));
    } else {
        message(3,'Bad Request');
        die();
    }
    unset($_tid,$_thread,$_thread_uid );
    message(0,'取消置顶成功！');
}

if($action == 'rfloor') {
	$pid = param(2);
	$pageno = param('pageno', 0);
	$delfloor = param('delfloor', false);
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists'));
	$m_s=',';
	if(empty($post['repeat_follow']))
	{
		$m_s=$repeat_follows='';
		if($pageno>0) message(-1, lang('post_not_exists'));
	}
	else $repeat_follows=$post['repeat_follow'];
	if($delfloor!==false)
	{
		empty($repeat_follows) AND message(-1, lang('post_not_exists'));
		$arrs=json_decode($repeat_follows,true);
		$n=0;
		$m_s=$message_json='';
		foreach($arrs as $arr){
			if($arr['fl']!=$delfloor)
			{
				if($n>0) $m_s=',';
				$message_json.=$m_s.'{"fl":"'.$arr['fl'].'","uid":"'.$arr['uid'].'","username":"'.$arr['username'].'","avatar_url":"'.$arr['avatar_url'].'","t_uid":"'.$arr['t_uid'].'","t_username":"'.$arr['t_username'].'","message":"'.str_replace(array('"','\\',),array('\"','\\'.'\\'),$arr['message']).'","update":"'.$arr['update'].'"}';
				$n++;
			}
		}
		if($message_json!=''){
			$message_json='['.$message_json.']';
			$r = db_update('post', array('pid'=>$pid), array('repeat_follow'=>$message_json, 'r_f_c'=>$n));
		}
		else $r = db_update('post', array('pid'=>$pid), array('repeat_follow'=>'', 'r_f_c'=>0, 'r_f_a'=>0));
		$r === FALSE AND message(-1, lang('update_post_failed'));
		message(0, lang('delete_successfully'));
	}
	$repeat_follows=substr($repeat_follows,1,-1);
	empty($repeat_follows) AND $m_s=$repeat_follows='';
	$tid = $post['tid'];
	$thread['uid']=$post['uid'];
	$thread['subject']=$post['message_fmt'];
	$thread['tid']=$post['tid'];
	if($pageno>0)
	{
		$return_message='';
		$r_f_g=setting_get('sl_repeat_follow_perpage');
		$pageno=min($pageno,$post['r_f_c']);
		$pageno=max($pageno,1);
		$repeat_follows=json_decode($post['repeat_follow'], true);
		$repeat_follows=array_slice($repeat_follows,($pageno-1)*$r_f_g,$r_f_g);
		$message_t=$deltag='';
		foreach($repeat_follows as $repeat_follow){
			if($repeat_follow['uid']==$uid || $post['floormanage']) $deltag='<a href="javascript:delrfloor('.$pid.',\''.$repeat_follow['fl'].'\');" class="post_update mr-2">删除</a>';
			if($repeat_follow['t_uid']>0 && $repeat_follow['t_username']!='') $message_t='回复 <a href="'.url("user-".$repeat_follow['t_uid']).'" class="text-muted font-weight-bold">'.$repeat_follow['t_username'].'</a>: ';
			$return_message.='<dd class="text-left media" id="pf_'.$pid.'_'.$repeat_follow['fl'].'"><a href="'.url("user-".$repeat_follow['uid']).'" class="mr-2"><img class="avatar-3" onerror="this.src=\'view/img/avatar.png\'"  src="'.$repeat_follow['avatar_url'].'"></a><div style="width:100%;"><span class="text-left"><a href="'.url("user-".$repeat_follow['uid']).'" class="text-muted font-weight-bold">'.$repeat_follow['username'].'</a>: '.$message_t.$repeat_follow['message'].'</span><div class="text-muted text-right">'.$deltag.humandate($repeat_follow['update']).'<a href="javascript:showform('.$pid.',\''.$repeat_follow['username'].'\');" class="post_update ml-2">回复</a></div></div></dd>';
			$message_t=$deltag='';
		}
		message(0,$return_message.'<div id="pushfloor_'.$pid.'" style="display:none;"></div>');
	}
	$t_username=$message_t='';
	$message = param('message', '', FALSE);
	$t_uid = 0;
	$t_username=trim(str_replace('回复','',strchr($message,':',true)));
	if($t_username!='')
	{
		$t_u = user_read_by_username($t_username);
		if (!$t_u || empty($t_u['uid'])) $t_username='';
		else
		{
			$message=trim(strchr($message,':'),':');
			$t_uid=$thread['uid']=$t_u['uid'];
			$message_t='回复 <a href="'.url("user-".$t_uid).'" class="text-muted font-weight-bold">'.$t_username.'</a>: ';
		}
	}
	$message = htmlspecialchars($message);
	$message = trim(xn_html_safe($message));
	$message = preg_replace("#[ ]{2,}#is"," ",str_replace(array("\n","\r","\t"),array(' ',' ',' '),$message));
	if(empty($message) || $message=='') message('message'.$pid, lang('please_input_message'));
	xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));
	if(function_exists("notice_send")){
		$thread['subject'] = notice_substr($thread['subject'], 20);
		$notice_message = '<div class="comment-info"><a class="mr-1 text-grey" href="'.url("thread-$thread[tid]").'#'.$pid.'">'.lang('notice_lang_comment').'</a>'.lang('notice_message_replytoyou').'<a href="'.url("thread-$thread[tid]").'#'.$pid.'">《回帖：'.$thread['subject'].'》</a></div><div class="single-comment"><a href="'.url("thread-$thread[tid]").'#'.$pid.'">'.notice_substr($message, 40, FALSE).'</a></div>';
		$recvuid = $thread['uid'];
		notice_send($uid, $recvuid, $notice_message, 2);
	}
	$r_f_c=$post['r_f_c']+1;
	$r_f_a=$post['r_f_a']+1;
	$return_message='<dd class="text-left media" id="pf_'.$pid.'_'.$r_f_a.'"><a href="'.url("user-".$uid).'" class="mr-2"><img class="avatar-3" onerror="this.src=\'view/img/avatar.png\'"  src="'.$user['avatar_url'].'"></a><div style="width:100%;"><span class="text-left"><a href="'.url("user-".$uid).'" class="text-muted font-weight-bold">'.$user['username'].'</a>: '.$message_t.$message.'</span><div class="text-muted text-right"><a href="javascript:delrfloor('.$pid.',\''.$r_f_a.'\');" class="post_update mr-2">删除</a>'.humandate($time).'<a href="javascript:showform('.$pid.',\''.$user['username'].'\');" class="post_update ml-2">回复</a></div></div></dd>';
	$dir = substr(sprintf("%09d", $user['uid']), 0, 3);
	$user_face=$conf['upload_url']."avatar/$dir/$uid.png";
	$message='['.$repeat_follows.$m_s.'{"fl":"'.$r_f_a.'","uid":"'.$uid.'","username":"'.$user['username'].'","avatar_url":"'.$user_face.'","t_uid":"'.$t_uid.'","t_username":"'.$t_username.'","message":"'.str_replace(array('"','\\'),array('\"','\\'.'\\'),$message).'","update":"'.$time.'"}]';
	$r = db_update('post', array('pid'=>$pid), array('repeat_follow'=>$message, 'r_f_c'=>$r_f_c, 'r_f_a'=>$r_f_a));
	$r === FALSE AND message(-1, lang('update_post_failed'));
	message(0,$return_message);
}

if($action == 'show_all_reply')
    include _include(APP_PATH.'plugin/zaesky_theme_light/view/htm/show_all_reply.htm');



if($action == 'create') {
	
	$tid = param(2);
	$quick = param(3);
	$quotepid = param(4);
		
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists'));
	
	$fid = $thread['fid'];
	
	$forum = forum_read($fid);
	empty($forum) AND message(-1, lang('forum_not_exists'));
	
	$r = forum_access_user($fid, $gid, 'allowpost');
	if(!$r) {
		message(-1, lang('user_group_insufficient_privilege'));
	}
	
	($thread['closed'] && ($gid == 0 || $gid > 5)) AND message(-1, lang('thread_has_already_closed'));
	
		!ipaccess_check($longip, 'posts') AND message(-1, '您的 IP 今日回帖数达到上限。');
	!ipaccess_check_seriate_posts($tid) AND message(-1, '您的 IP 今日连续发帖数已经达到上限。');
		// todo:
		$tagids = tag_thread_find_tagid_by_tid($tid, $forum['tagcatelist']);

	
	if($method == 'GET') {
		
		
		
		$header['title'] = lang('post_create');
		$header['mobile_title'] = lang('post_create');
		$header['mobile_link'] = url("thread-$tid");

		include _include(APP_PATH.'view/htm/post.htm');
		
	} else {
		
		
		qt_check_sensitive_word($_REQUEST['message'], 'post_sensitive_words', $qt_error) AND message('message', lang('post_contain_sensitive_word') . $qt_error);
$kv_vcode = kv_get('vcode');
if(!empty($kv_vcode['vcode_post_create_on'])) {
	$vcode_post = param('vcode');
	$vcode_sess = _SESSION('vcode');
	strtolower($vcode_post) != strtolower($vcode_sess) AND message('vcode', '验证码不正确');
}
$set=setting_get('tt_credits');
$credits = $set['post_exp'];$credits_op = $credits>0?'+':'';
$golds = $set['post_gold'];$golds_op = $golds>0?'+':'';
$rmbs=$set['post_rmb'];$rmbs_op = $rmbs>0?'+':'';
if(($credits<0&&($user['credits']+$credits<0))||($golds<0&&($user['golds']+$golds<0))||($rmbs<0&&($user['rmbs']+$rmbs<0)))
    {message(-1,lang('credit_no_enough'));die();}
$c_limit =$set['limit'] ; $add_credit=1;
if($c_limit!=0) {
    $todayTime = strtotime(date('Y-m-d',time()))-1;
    $todayThread = db_count('post',array('create_date' => array('>'=>$todayTime),'uid'=>$uid,'isfirst'=>'0'));
    if($c_limit<=$todayThread) $add_credit=0;
}
		
		$message = param('message', '', FALSE);
		empty($message) AND message('message', lang('please_input_message'));
		
		$doctype = param('doctype', 0);
		xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));
		
		$thread['top'] > 0 AND thread_top_cache_delete();
		
		$quotepid = param('quotepid', 0);
		$quotepost = post__read($quotepid);
		(!$quotepost || $quotepost['tid'] != $tid) AND $quotepid = 0;
		
		$post = array(
			'tid'=>$tid,
			'uid'=>$uid,
			'create_date'=>$time,
			'userip'=>$longip,
			'isfirst'=>0,
			'doctype'=>$doctype,
			'quotepid'=>$quotepid,
			'message'=>$message,
		);
		$pid = post_create($post, $fid, $gid);
		empty($pid) AND message(-1, lang('create_post_failed'));
		
		// thread_top_create($fid, $tid);

		$post = post_read($pid);
		$post['floor'] = $thread['posts'] + 2;
		$postlist = array($post);
		
		$allowpost = forum_access_user($fid, $gid, 'allowpost');
		$allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
		$allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
		
		

	$thread['subject'] = notice_substr($thread['subject'], 20);

	// 回复
	$notice_message = '<div class="comment-info"><a class="mr-1 text-grey" href="'.url("thread-$thread[tid]").'#'.$pid.'">'.lang('notice_lang_comment').'</a>'.lang('notice_message_replytoyou').'<a href="'.url("thread-$thread[tid]").'">《'.$thread['subject'].'》</a></div><div class="single-comment"><a href="'.url("thread-$thread[tid]").'#'.$pid.'">'.notice_substr($message, 40, FALSE).'</a></div>';
	$recvuid = $thread['uid'];

	
	$recvuid != $quotepost['uid'] AND notice_send($uid, $recvuid, $notice_message, 2); //$quotepost['uid']可能是null，但不影响逻辑

	// 引用
	if(!empty($quotepid) && $quotepid > 0) {

		

		 
		 $notice_quote_message = '<div class="comment-info"><a class="mr-1 text-grey" href="'.url("thread-$thread[tid]").'#'.$pid.'">'.lang('notice_lang_reply').'</a>'.lang('notice_message_replytoyou_at').'<a href="'.url("thread-$thread[tid]").'">《'.$thread['subject'].'》</a>'.lang('notice_message_replytoyou_for').'</div><div class="quote-comment">'.notice_substr($quotepost['message'], 40, FALSE).'</div><div class="reply-comment"><a href="'.url("thread-$thread[tid]").'#'.$pid.'">'.notice_substr($message, 40, FALSE).'</a></div>';



		notice_send($uid, $quotepost['uid'], $notice_quote_message, 2);	
	}


unset($_SESSION['vcode']);
$r=db_find_one('post',array('pid'=>$pid));
if($r){
   $message=$r['message'];
$n = preg_match_all("/(?:[^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$message,$result);
if($n>0){
	$message=str_replace('[',' [',$message);
	$message=str_replace(']','] ',$message);
	$newm="\${1}<a href=\"\${2}\" target=\"_blank\" _href=\"\${2}\"><span style=\"color:#FF2300\">\${2}</span></a>";
	$message=preg_replace("/([^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$newm,$message);
	$message=str_replace(' [ ','[',$message);
	$message=str_replace('] ',']',$message);
}
   db_update('post',array('pid'=>$pid),array('message'=>$message,'message_fmt'=>$message));
}		ipaccess_inc($longip, 'posts');
$return_html = param('return_html', 0);
$update_array = array();
if((($add_credit==1)||($add_credit==0&& $credits<0))&&$credits!=0) $update_array['credits+']=$credits;
if((($add_credit==1)||($add_credit==0&& $golds<0))&&$golds!=0) $update_array['golds+']=$golds;
if((($add_credit==1)||($add_credit==0&& $rmbs<0))&&$rmbs!=0) $update_array['rmbs+']=$rmbs;
$uid AND $update_array AND user_update($uid, $update_array);
$uid AND $update_array AND $user['gid']>=100 AND user_update_group($uid);
$message = '';
isset($update_array['credits+']) AND $message .= lang('credits1').$credits_op.$credits.' ' ;
isset($update_array['golds+']) AND $message .= lang('credits2').$golds_op.$golds.' ' ;
isset($update_array['rmbs+']) AND $message .= lang('credits3').$rmbs_op.$rmbs ;
if($return_html) {
		$filelist = array();ob_start();
		include _include(APP_PATH.'view/htm/post_list.inc.htm');
		$s = ob_get_clean();message(0, $s);
} else {
		$message = $message ? $message : lang('create_post_sucessfully'); message(0, $message);
}

		$return_html = param('return_html', 0);
		$sg_group = setting_get('sg_group');
		$uid AND user__update($uid, array('credits+'=>$sg_group['post_credits']));
		user_update_group($uid);
		if($return_html) {
			$filelist = array();
			ob_start();
			$sg_group_pid = $pid;
			include _include(APP_PATH.'view/htm/post_list.inc.htm');
			$s = ob_get_clean();
			message(0, $s);
		} else {
			
			message(0, lang('create_post_sucessfully').lang('sg_creditsplus',  array('credits'=>$sg_group['post_credits'])));
		}

		
		// 直接返回帖子的 html
		// return the html string to browser.
		$return_html = param('return_html', 0);
		if($return_html) {
			$filelist = array();
			ob_start();
			include _include(APP_PATH.'view/htm/post_list.inc.htm');
			$s = ob_get_clean();
						
			message(0, $s);
		} else {
			message(0, lang('create_post_sucessfully'));
		}
	
	}
	
} elseif($action == 'update') {

	$pid = param(2);
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists:'));
	
	$tid = $post['tid'];
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists:'));
	
	$fid = $thread['fid'];
	$forum = forum_read($fid);
	empty($forum) AND message(-1, lang('forum_not_exists:'));
	
	$isfirst = $post['isfirst'];
	
	!forum_access_user($fid, $gid, 'allowpost') AND message(-1, lang('user_group_insufficient_privilege'));
	$allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
	!$allowupdate AND !$post['allowupdate'] AND message(-1, lang('have_no_privilege_to_update'));
	!$allowupdate AND $thread['closed'] AND message(-1, lang('thread_has_already_closed'));
	
	
	
	if($method == 'GET') {
		
		if($gid != 1) message(-1, '本站不能编辑帖子，请联系管理员');if($gid > 5 && isset($light_config['thread_user_upd']) && $light_config['thread_user_upd'] == 1) message(-1, lang('no_permission_update'));
		
		$forumlist_allowthread = forum_list_access_filter($forumlist, $gid, 'allowthread');
		$forumarr = xn_json_encode(arrlist_key_values($forumlist_allowthread, 'fid', 'name'));
		
		// 如果为数据库减肥，则 message 可能会被设置为空。
		// if lost weight for the database, set the message field empty.
		$post['message'] = htmlspecialchars($post['message'] ? $post['message'] : $post['message_fmt']);
		
		$attachlist = $imagelist = $filelist = array();
		if($post['files']) {
			list($attachlist, $imagelist, $filelist) = attach_find_by_pid($pid);
		}
		
		
$content_num = $thread['content_buy'];
$content_type = $thread['content_buy_type']=='0'?'1': $thread['content_buy_type'];
if($group['allowsell']=="1") {
    $input['content_num_status'] = form_radio_yes_no('content_num_status', $content_num > 0 ? 1 : 0);
}

$pnumber = $thread['readp'];
$input['readp_status'] = form_radio_yes_no('readp_status', $pnumber > 0 ? 1 : 0);

		// todo:
		$tagids = tag_thread_find_tagid_by_tid($tid, $forum['tagcatelist']);
				
			// 编辑器支持 HTML 编辑
			if($post['doctype'] == 1) {
				$post['message'] = htmlspecialchars($post['message_fmt']);
			}
		
		include _include(APP_PATH.'view/htm/post.htm');
		
	} elseif($method == 'POST') {
		
		$subject = htmlspecialchars(param('subject', '', FALSE));
		$message = param('message', '', FALSE);
		$doctype = param('doctype', 0);
		
		
		qt_check_sensitive_word($subject, 'post_sensitive_words', $qt_error) AND message('subject', lang('thread_contain_sensitive_word') . $qt_error);
		qt_check_sensitive_word($message, 'post_sensitive_words', $qt_error) AND message('message', lang('post_contain_sensitive_word') . $qt_error);
$kv_vcode = kv_get('vcode');
if(!empty($kv_vcode['vcode_post_create_on'])) {
	$vcode_post = param('vcode');
	$vcode_sess = _SESSION('vcode');
	strtolower($vcode_post) != strtolower($vcode_sess) AND message('vcode', '验证码不正确');
}
if($gid != 1) message(-1, '本站不能编辑帖子，请联系管理员');if($gid > 5 && isset($light_config['thread_user_upd']) && $light_config['thread_user_upd'] == 1) message(-1, lang('no_permission_update'));
		
		empty($message) AND message('message', lang('please_input_message'));
		mb_strlen($message, 'UTF-8') > 2048000 AND message('message', lang('message_too_long'));
		
		$arr = array();
		if($isfirst) {
			$newfid = param('fid');
			$forum = forum_read($newfid);
			empty($forum) AND message('fid', lang('forum_not_exists'));
			
			if($fid != $newfid) {
				!forum_access_user($fid, $gid, 'allowthread') AND message(-1, lang('user_group_insufficient_privilege'));
				$post['uid'] != $uid AND !forum_access_mod($fid, $gid, 'allowupdate') AND message(-1, lang('user_group_insufficient_privilege'));
				$arr['fid'] = $newfid;
			}
			if($subject != $thread['subject']) {
				mb_strlen($subject, 'UTF-8') > 80 AND message('subject', lang('subject_max_length', array('max'=>80)));
				$arr['subject'] = $subject;
			}
			$arr AND thread_update($tid, $arr) === FALSE AND message(-1, lang('update_thread_failed'));
		}
		$r = post_update($pid, array('doctype'=>$doctype, 'message'=>$message));
		$r === FALSE AND message(-1, lang('update_post_failed'));
		
		
unset($_SESSION['vcode']);


if($isfirst) {
    thread__update($tid,['passcode' => param('passcode','')]);
}

if($group['allowsell']=="1") {
    $content_num_status = param('content_num_status');
    $content_num = param('content_num');//下面添加
	if($content_num < 0 ){//判断购买主题货币值小于零
		$content_num = 1;//小于零强制写为一
	}
    $content_type = credits_get_content_type_by_name(param('content_type'));
    if ($content_num_status && $content_num)
        db_update('thread', array('tid' => $tid), array('content_buy' => $content_num, 'content_buy_type' => $content_type));
     else
        db_update('thread', array('tid' => $tid), array('content_buy' => 0));
}

$pnumber = param('readp'); $pstatus=param('readp_status');
if ($pstatus && $pnumber>=0)
    db_update('thread', array('tid' => $tid), array('readp' => $pnumber));
else
    db_update('thread', array('tid' => $tid), array('readp' => 0));

	$r = db_find_one('post',array('pid'=>$pid));
	if($r){
		$n = preg_match_all("/(?:[^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$r['message'],$result);
		if($n>0){
			$r['message']=str_replace('[ttDown]http','[ttDown]',$r['message']);
			$r['message']=str_replace('[',' [',$r['message']);
			$r['message']=str_replace(']','] ',$r['message']);
			$newm="\${1}<a href=\"\${2}\" target=\"_blank\" _href=\"\${2}\"><span style=\"color:#FF2300\">\${2}</span></a>";
			$r['message']=preg_replace("/([^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$newm,$r['message']);
			$r['message']=str_replace(' [ ','[',$r['message']);
			$r['message']=str_replace('] ',']',$r['message']);
			$r['message']=str_replace('[ttDown]','[ttDown]http',$r['message']);
			db_update('post',array('pid'=>$pid),array('message'=>$r['message'],'message_fmt'=>$r['message']));
		}
	}
		// todo:
		/*
		$tag_cate_id_arr = param('tag_cate_id', array(0));
		
		
		$tagids_new = array_values($tag_cate_id_arr);
		$tagids_old = tag_thread_find_tagid_by_tid($tid);
		//print_r($tagids_new);print_r($tagids_old);exit;
		//新增的、删除的 
		$tag_id_delete = array_diff($tagids_old, $tagids_new);
		$tag_id_add = array_diff($tagids_new, $tagids_old);
		foreach($tag_id_delete as $tagid) {
			tag_thread_delete($tagid, $tid);
		}
		foreach($tag_id_add as $tagid) {
			tag_thread_create($tagid, $tid);
		}
		thread_update($tid, array('tagids'=>'', 'tagids_time'=>0));

		*/
		
		if($isfirst) {
			$tagids = param('tagid', array(0));
			$tagcatemap = $forum['tagcatemap'];
			foreach($forum['tagcatemap'] as $cate) {
				$defaulttagid = $cate['defaulttagid'];
				$isforce = $cate['isforce'];
				$catetags = array_keys($cate['tagmap']);
				$intersect = array_intersect($catetags, $tagids); // 比较数组交集
				// 判断是否强制
				if($isforce) {
					if(empty($intersect)) {
						message(-1, '请选择 ['.$cate['name'].']');
					}
				}
				// 判断是否默认
				if($defaulttagid) {
					if(empty($intersect)) {
						array_push($tagids, $defaulttagid);
					}
				}
				
			}
			
			$tagids = array_diff($tagids, array(0));
			$tagids_new = $tagids;
			$tagids_old = tag_thread_find_tagid_by_tid($tid, $forum['tagcatelist']);
			$tag_id_delete = array_diff($tagids_old, $tagids_new);
			$tag_id_add = array_diff($tagids_new, $tagids_old);
			if($tag_id_delete) {
				foreach($tag_id_delete as $tagid) {
					$tagid AND tag_thread_delete($tagid, $tid);
				}
			}
			if($tag_id_add) {
				foreach($tag_id_add as $tagid) {
					$tagid AND tag_thread_create($tagid, $tid);
				}
			}
			thread_update($tid, array('tagids'=>'', 'tagids_time'=>0));
			/*
			foreach($tagids as $tagid) {
				$tagid AND tag_thread_create($tagid, $tid);
			}*/
		}

		
		message(0, lang('update_successfully'));
		//message(0, array('pid'=>$pid, 'subject'=>$subject, 'message'=>$message));
	}
	
} elseif($action == 'delete') {

	$pid = param(2, 0);
	
	if($gid != 1) message(-1, '没有权限删除帖子，请联系管理员');if($gid > 5 && isset($light_config['thread_user_del']) && $light_config['thread_user_del'] == 1) message(-1, lang('no_permission_delete'));
	
	if($method != 'POST') message(-1, lang('method_error'));
	
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists'));
	
	$tid = $post['tid'];
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists'));
	
	$fid = $thread['fid'];
	$forum = forum_read($fid);
	empty($forum) AND message(-1, lang('forum_not_exists'));
	
	$isfirst = $post['isfirst'];
	
	!forum_access_user($fid, $gid, 'allowpost') AND message(-1, lang('user_group_insufficient_privilege'));
	$allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
	!$allowdelete AND !$post['allowdelete'] AND message(-1, lang('insufficient_delete_privilege'));
	!$allowdelete AND $thread['closed'] AND message(-1, lang('thread_has_already_closed'));
	
	

	if($isfirst) {
		thread_delete($tid);
	} else {
		post_delete($pid);
		//post_list_cache_delete($tid);
	}
	
	
	
	message(0, lang('delete_successfully'));

}



elseif ($action == 'post_like') {

	$header['title'] = lang('haya_post_like')." - " . $conf['sitename'];
	
	if (!$uid) {
		message(0, lang('haya_post_like_login_like_tip'));
	}
	
	
	
	if ($method == 'POST') {

		$pid = param('pid');

		$post = post_read($pid);
		empty($post) AND message(0, lang('post_not_exists'));

		if ($post['isfirst'] == 1) {
			if (isset($haya_post_like_config['open_thread'])
				&& $haya_post_like_config['open_thread'] != 1
			) {
				message(0, lang('haya_post_like_close_thread_tip'));
			}
		} else {
			if (isset($haya_post_like_config['open_post'])
				&& $haya_post_like_config['open_post'] != 1
			) {
				message(0, lang('haya_post_like_close_post_tip'));
			}
		}
	
		haya_post_like_cache_delete($post['tid']);
		
		$haya_post_like_check = haya_post_like_find_by_uid_and_pid($uid, $pid);
		
		$action2 = param(2, 'create');
		if ($action2 == 'create') {
			
			
			if (!empty($haya_post_like_check)) {
				message(0, lang('haya_post_like_user_has_like_tip'));
			}
			
			haya_post_like_create(array(
				'tid' => $post['tid'], 
				'pid' => $pid, 
				'uid' => $user['uid'],
				'create_date' => time(),
				'create_ip' => $longip,
			));			
			
			if (isset($haya_post_like_config['post_like_count_type'])
				&& $haya_post_like_config['post_like_count_type'] == 1
			) {
				$haya_post_like_count = haya_post_like_count(array('pid' => $pid));
				
				post__update($post['pid'], array('likes' => $haya_post_like_count));
				
				if ($post['isfirst'] == 1) {
					thread__update($post['tid'], array('likes' => $haya_post_like_count));
				}
			} else {
				$haya_post_like_count = intval($post['likes']) + 1;
				
				haya_post_like_loves($pid, 1);
				
				if ($post['isfirst'] == 1) {
					thread__update($post['tid'], array('likes+' => 1));
				}
			}
			
			$haya_post_like_msg = array(
				'count' => intval($haya_post_like_count),
				'msg' => lang('haya_post_like_like_success_tip'),
			);
			
			

if (function_exists("notice_send")) {
	
	
	$notice_user = '<a href="'.url('user-'.$user['uid']).'" target="_blank"><img class="avatar-1" src="'.$user['avatar_url'].'"> '.$user['username'].'</a>';

	$thread = thread_read($post['tid']);
	$thread['subject'] = notice_substr($thread['subject'], 20);
	$notice_thread = '<a target="_blank" href="'.url('thread-'.$post['tid']).'">《'.$thread['subject'].'》</a>';

	$post['message'] = htmlspecialchars(strip_tags($post['message']));
	$post['message'] = notice_substr($post['message'], 40);
	$notice_post = '<a target="_blank" href="'.url('thread-'.$post['tid'].'-1').'#'.$post['pid'].'">【'.$post['message'].'】</a>';
	
	if ($post['isfirst'] == 1) {
		$notice_msg_tpl = lang('haya_post_like_send_notice_for_thread');
		
		
	} else {
		$notice_msg_tpl = lang('haya_post_like_send_notice_for_post');
		
		
	}
	
	$notice_msg = str_replace(
		array('{thread}', '{post}', '{user}'),
		array($notice_thread, $notice_post, $notice_user),
		$notice_msg_tpl
	);

	notice_send($user['uid'], $post['uid'], $notice_msg, 150);
	
	
}


			
			message(1, $haya_post_like_msg);
		} elseif ($action2 == 'delete') {
			
			
			if (isset($haya_post_like_config['like_is_delete'])
				&& $haya_post_like_config['like_is_delete'] != 1
			) {
				message(0, lang('haya_post_like_no_unlike_tip'));
			}
			
			if (empty($haya_post_like_check)) {
				message(0, lang('haya_post_like_user_no_like_tip'));
			}
			
			$post_like = haya_post_like_read_by_uid_and_pid($uid, $pid);

			$delete_time = intval($haya_post_like_config['delete_time']);
			if ($post_like['create_date'] + $delete_time > time()) {
				message(0, lang('haya_post_like_no_fast_like_tip'));
			}
			
			haya_post_like_delete_by_pid_and_uid($pid, $user['uid']);
			
			if (isset($haya_post_like_config['post_like_count_type'])
				&& $haya_post_like_config['post_like_count_type'] == 1
			) {
				$haya_post_like_count = haya_post_like_count(array('pid' => $pid));
				
				post__update($post['pid'], array('likes' => $haya_post_like_count));
				
				if ($post['isfirst'] == 1) {
					thread__update($post['tid'], array('likes' => $haya_post_like_count));
				}
			} else {
				$haya_post_like_count = MAX(0, intval($post['likes']) - 1);
				
				haya_post_like_loves($pid, -1);
				
				if ($post['isfirst'] == 1) {
					$haya_post_like_thread = thread__read($post['tid']);
					
					if ($haya_post_like_thread['likes'] > 0) {
						thread__update($post['tid'], array('likes-' => 1));
					}
				}
			}			
			
			$haya_post_like_msg = array(
				'count' => intval($haya_post_like_count),
				'msg' => lang('haya_post_like_unlike_success_tip'),
			);
			
			
			
			message(1, $haya_post_like_msg);
		}
		
		
		
		message(0, lang('haya_post_like_like_error_tip'));	
	}
	
	
	
	message(0, lang('haya_post_like_like_error_tip'));

}




?>