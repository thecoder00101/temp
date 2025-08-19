<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);



if ($action == 'favorite') {

	$header['title'] = lang('haya_favorite') . " - " . $conf['sitename'];

	$haya_favorite_config = setting_get('haya_favorite');
	
	if ($method == 'POST') {

		$tid = param('tid');

		$thread = thread_read($tid);
		empty($thread) AND message(0, lang('thread_not_exists'));
		$haya_check_favorite = haya_favorite_find_by_uid_and_tid($uid, $tid);
		
		$haya_favorite_user_favorite_count = isset($haya_favorite_config['user_favorite_count']) ? intval($haya_favorite_config['user_favorite_count']) : 20;
		
		$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
		
		ob_start();
		include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
		$haya_favorite_user_html = ob_get_clean();

		message(1, $haya_favorite_user_html);
	}
	
	message(1, lang('haya_favorite_error'));

} else




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
	$repeat_follows=substr($repeat_follows,1,-1);
	empty($repeat_follows) AND $m_s=$repeat_follows='';
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
}

if($action == 'cPay'){
	$tid = param(2);
	$content_pay = db_find_one('paylist', array('tid' => $tid, 'uid' => $uid, 'type' => 1));
	if(!$content_pay){
		if(!$user) message(-2, lang('login_first'));
		$thread = thread_read($tid);
		empty($thread) AND message(-1, lang('thread_not_exists:'));
		$operation_credit_area;
		switch($thread['content_buy_type']) {
            case '1':$operation_credit_area='credits';break;
            case '2':$operation_credit_area='golds';break;
            case '3':$operation_credit_area='rmbs';break;
            default:$operation_credit_area='rmbs';break;
        }
		if($thread['content_buy']!=0 && $user[$operation_credit_area]<$thread['content_buy'])
			message(-3, str_replace(lang('credits'),'<span style="color:dodgerblue;font-weight:bold;">'.lang('credits'.$thread['content_buy_type']).'</span>',lang('credit_no_enough')));
		db_insert('paylist',array('tid' => $tid, 'uid' => $uid, 'credit_type'=>(int)$thread['content_buy_type'],'num' => (int)$thread['content_buy'], 'type' => 1, 'paytime' => time()));
		$now_golds = $user[$operation_credit_area]-$thread['content_buy'];
		db_update('user', array('uid' => $uid), array($operation_credit_area => $now_golds));
		$origin = db_find_one('user', array('uid' => $thread['uid']));
		$current = $origin[$operation_credit_area]+$thread['content_buy'];
		db_update('user', array('uid' => $thread['uid']), array($operation_credit_area => $current));
        $uid AND $user['gid']>=100 AND user_update_group($uid);
        $uid AND db_insert('user_pay',array('uid'=>$uid,'status'=>1,'num'=>$thread['content_buy'],'type'=>'4','credit_type'=>$thread['content_buy_type'],'code'=>'帖子,(<a href="thread-'.$thread['tid'].'.htm">'.$thread['tid'].'</a>)','time'=>time()));
		message(0, lang('pay_success'));
	}else
		message(0, lang('pay_success'));
}elseif($action == 'sPay') {
    $tid = param(2);
    include _include(APP_PATH . 'plugin/tt_credits/view/htm/tt_buy_list.htm');
    return;
}


// 发表主题帖 | create new thread
if($action == 'create') {
	
		!ipaccess_check($longip, 'threads') AND message(-1, '您的 IP 今日主题数达到上限。');
	!ipaccess_check_seriate_threads() AND message(-1, '您的 IP 今日连续主题数已经达到上限。');
		
	user_login_check();

	if($method == 'GET') {
		
		
		
		$fid = param(2, 0);
		$forum = $fid ? forum_read($fid) : array();
		
		$forumlist_allowthread = forum_list_access_filter($forumlist, $gid, 'allowthread');
		$forumarr = xn_json_encode(arrlist_key_values($forumlist_allowthread, 'fid', 'name'));
		if(empty($forumlist_allowthread)) {
			message(-1, lang('user_group_insufficient_privilege'));
		}
		
		$header['title'] = lang('create_thread');
		$header['mobile_title'] = $fid ? $forum['name'] : '';
		$header['mobile_linke'] = url("forum-$fid");
		
		
$content_num = 0;
$content_num_type = 1;
if($group['allowsell']=="1") {
    $input['content_num_status'] = form_radio_yes_no('content_num_status', 0);
}

$pnumber=0;
$input['readp_status'] = form_radio_yes_no('readp_status', 0);

		
		include _include(APP_PATH.'view/htm/post.htm');
		
	} else {
		
		

$kv_vcode = kv_get('vcode');
if(!empty($kv_vcode['vcode_thread_create_on'])) {
	$vcode_post = param('vcode');
	$vcode_sess = _SESSION('vcode');
	strtolower($vcode_post) != strtolower($vcode_sess) AND message('vcode', '验证码不正确');
}

$set = setting_get('tt_credits');
$credits = $set['thread_exp'];$rmbs = $set['thread_rmb'];$golds = $set['thread_gold'];
$golds_op = $golds>0?'+':'';$credits_op = $credits>0?'+':'';$rmbs_op = $rmbs>0?'+':'';
if(($credits<0&&($user['credits']+$credits<0))||($golds<0&&($user['golds']+$golds<0))||($rmbs<0&&($user['rmbs']+$rmbs<0)))
{message(-1,lang('credit_no_enough'));die();}
$c_limit =$set['limit'] ; $add_credit=1;
if($c_limit!=0) {
    $todayTime = strtotime(date('Y-m-d',time()))-1;
    $todayThread = db_count('post',array('create_date' => array('>'=>$todayTime),'uid'=>$uid,'isfirst'=>'1'));
    if($c_limit<=$todayThread) $add_credit=0;
}

		
		$fid = param('fid', 0);
		$forum = forum_read($fid);
		empty($forum) AND message('fid', lang('forum_not_exists'));
		
		$r = forum_access_user($fid, $gid, 'allowthread');
		!$r AND message(-1, lang('user_group_insufficient_privilege'));
		
		$subject = htmlspecialchars(param('subject', '', FALSE));
		empty($subject) AND message('subject', lang('please_input_subject'));
		xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength'=>128)));
		
		$message = param('message', '', FALSE);
		empty($message) AND message('message', lang('please_input_message'));
		$doctype = param('doctype', 0);
		$doctype > 10 AND message(-1, lang('doc_type_not_supported'));
		xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));
		
		$thread = array (
			'fid'=>$fid,
			'uid'=>$uid,
			'sid'=>$sid,
			'subject'=>$subject,
			'message'=>$message,
			'time'=>$time,
			'longip'=>$longip,
			'doctype'=>$doctype,
		);
		
		
		qt_check_sensitive_word($thread['subject'], 'post_sensitive_words', $qt_error) AND message('subject', lang('thread_contain_sensitive_word') . $qt_error);
		qt_check_sensitive_word($thread['message'], 'post_sensitive_words', $qt_error) AND message('message', lang('post_contain_sensitive_word') . $qt_error);
		// todo:
		
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
					message(-1, '请选择'.$cate['name']);
				}
			}
			
		}
		

		
		$tid = thread_create($thread, $pid);
		$pid === FALSE AND message(-1, lang('create_post_failed'));
		$tid === FALSE AND message(-1, lang('create_thread_failed'));
		
		$pnumber = param('readp');$pstatus=param('readp_status');
if ($pstatus&& $pnumber>0)
    {db_update('thread', array('tid' => $tid), array('readp' => $pnumber));}
unset($_SESSION['vcode']);
unset($_SESSION['vcode_initpw_ok']);


thread__update($tid,['passcode' => param('passcode','')]);

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
	}		ipaccess_inc($longip, 'threads');
		// todo:
		/*
		$tag_cate_id_arr = param('tag_cate_id', array(0));
		foreach($tag_cate_id_arr as $tag_cate_id => $tagid) {
			tag_thread_create($tagid, $tid);
		}
		*/
		
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
					message(-1, '请选择'.$cate['name']);
				}
			}
			// 判断是否默认
			if($defaulttagid) {
				if(empty($intersect)) {
					array_push($tagids, $defaulttagid);
				}
			}
		}
		
		foreach($tagids as $tagid) {
			$tagid AND tag_thread_create($tagid, $tid);
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
}
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
message(0, lang('create_thread_sucessfully').' '.$message);

 message(0, lang('create_thread_sucessfully').$_SESSION['sg_group_message']);
 
		message(0, lang('create_thread_sucessfully'));
	}
	
// 帖子详情 | post detail
} else {
	
	// thread-{tid}-{page}-{keyword}.htm
	$tid = param(1, 0);
	$page = param(2, 1);
	$keyword = param(3);
	$pagesize = $conf['postlist_pagesize'];
	//$pagesize = 10;
	//$page == 1 AND $pagesize++;
	
	ipaccess_inc($longip, 'read_thread');
if( ipaccess_check($longip, 'read_thread') === false ) {
message(1,'您今日的查看帖子数量已达到上限。');
}
	
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists'));
	
	$fid = $thread['fid'];
	$forum = forum_read($fid);
	empty($forum) AND message(3, lang('forum_not_exists'));
	
	$postlist = post_find_by_tid($tid, $page, $pagesize);
	empty($postlist) AND message(4, lang('post_not_exists'));
	
	if($page == 1) {
		// 确保主题帖存在，如果不存在则手动获取
        if(empty($postlist[$thread['firstpid']])) {
            $first = post_read($thread['firstpid']);
            if(empty($first)) {
                message(-1, lang('data_malformation'));
            }
        } else {
            $first = $postlist[$thread['firstpid']];
            unset($postlist[$thread['firstpid']]);
        }
        $attachlist = $imagelist = $filelist = array();
		
		// 如果是大站，可以用单独的点击服务，减少 db 压力
		// if request is huge, separate it from mysql server
		thread_inc_views($tid);
	} else {
		$first = post_read($thread['firstpid']);
	}
	
	$keywordurl = '';
	if($keyword) {
		$thread['subject'] = post_highlight_keyword($thread['subject'], $keyword);
		//$first['message'] = post_highlight_keyword($first['subject']);
		$keywordurl = "-$keyword";
	}
	$allowpost = forum_access_user($fid, $gid, 'allowpost') ? 1 : 0;
	$allowupdate = forum_access_mod($fid, $gid, 'allowupdate') ? 1 : 0;
	$allowdelete = forum_access_mod($fid, $gid, 'allowdelete') ? 1 : 0;
	
	forum_access_user($fid, $gid, 'allowread') OR message(-1, lang('user_group_insufficient_privilege'));
	
	$pagination = pagination(url("thread-$tid-{page}$keywordurl"), $thread['posts'] + 1, $page, $pagesize);
	
	$header['title'] = $thread['subject'].'-'.$forum['name'].'-'.$conf['sitename']; 
	//$header['mobile_title'] = lang('thread_detail');
	$header['mobile_title'] = $forum['name'];;
	$header['mobile_link'] = url("forum-$fid");
	$header['keywords'] = ''; 
	$header['description'] = $thread['subject'];
	$_SESSION['fid'] = $fid;
	
	
	
	
    if($page == 1 && $first['user']['gid'] == 7){
        if($user['gid'] == 1){
            $first['message_fmt'] = $first['message_fmt_fox'];
        }
    }


if (isset($haya_post_like_config['open_post'])
	&& $haya_post_like_config['open_post'] == 1
) {
	$hot_like_post_size = intval($haya_post_like_config['hot_like_post_size']) + 1;
	$hot_like_post_low_count = intval($haya_post_like_config['hot_like_post_low_count']);
	
	$haya_post_like_post_ids = array();
	if (!empty($postlist)) {
		foreach ($postlist as $haya_post_like_post) {
			$haya_post_like_post_ids[] = $haya_post_like_post['pid'];
		}
	}
	
	$haya_post_like_life_time = isset($haya_post_like_config['hot_like_life_time']) ? intval($haya_post_like_config['hot_like_life_time']) : 86400;
	$haya_post_like_hot_posts = haya_post_like_find_hot_posts_by_tid_cache($thread['tid'], $hot_like_post_size, $hot_like_post_low_count, $haya_post_like_life_time);
	
	if (!empty($haya_post_like_hot_posts)) {
		if (isset($haya_post_like_config['hot_like_isfirst'])
			&& $haya_post_like_config['hot_like_isfirst'] == 1
		) {
			$hot_like_isfirst = true;
		} else {
			$hot_like_isfirst = false;
		}
		
		$haya_post_like_hot_post_isfirst = false;
		foreach ($haya_post_like_hot_posts as $haya_post_like_hot_post_key => $haya_post_like_hot_post) {
			if ($haya_post_like_hot_post['isfirst'] == 1 && !$hot_like_isfirst) {
				unset($haya_post_like_hot_posts[$haya_post_like_hot_post_key]);
				$haya_post_like_hot_post_isfirst = true;
			} else {
				$haya_post_like_post_ids[] = $haya_post_like_hot_post['pid'];
				
				// 移除楼层
				$haya_post_like_hot_posts[$haya_post_like_hot_post_key]['floor'] = '';
			}
		}
		
		if (!$haya_post_like_hot_post_isfirst && (count($haya_post_like_hot_posts)) >= $hot_like_post_size) {
			array_pop($haya_post_like_hot_posts);
		}
	}
	
	$haya_post_like_pids = haya_post_like_find_by_pids_and_uid($haya_post_like_post_ids, $uid, count($haya_post_like_post_ids));
}


$spay_url = url('thread-sPay-'.$tid);
if($thread['content_buy_type']=='3') {$thread['content_buy'];}
if($route=='mip')
    $html_pay='<strong>您好，本帖含有付费内容，请您点击下方“查看完整版网页”获取！</strong>';
else
    $html_pay='<div class="alert alert-warning" role="alert"> <i class="icon-shopping-cart" style="color:gold;" aria-hidden="true" title="ttPay"></i> '.$conf['sitename'].' - '.lang("purchase").'<hr/>'.lang("have_pay").' <span style="font-weight: bold;">'.$thread['content_buy'].lang('credits'.$thread['content_buy_type']).' </span>'.lang("after_see").'<button id="b_p" type="submit" style="text-decoration: none; color:white;float:right;" class="btn btn-warning mr-2" data-loading-text="'.lang('submiting').'..." data-active="'.url('thread-cPay-'.$tid).'">'.lang("purchase").'</button><div style="clear:both;"></div></div>';
$preg_pay = preg_match_all('/\[ttPay\](.*?)\[\/ttPay\]/s',$first['message_fmt'],$array);
$first['purchased']='1';
$content_pay = db_find_one('paylist', array('tid' => $tid, 'uid' => $uid, 'type' => 1)); $is_set=0;
if($thread['content_buy']){
	if($preg_pay){
		$array_count = count($array[0]);
		for($i=0;$i<$array_count;$i++){
			$a = $array[0][$i];
			$b = '<div class="alert alert-success" role="alert"> <i class="icon-shopping-cart" style="color:green;" aria-hidden="true" title="ttPay"></i> '.$conf['sitename'].' - '.lang("see_paid").'<div style="float:right;"><a href="'.$spay_url.'">查看购买记录</a></div><hr/>'.$array[1][$i].'</div>';
			if($content_pay||$thread['uid']==$uid) $first['message_fmt'] = str_replace($a,$b,$first['message_fmt']);

			else $first['message_fmt'] = str_replace($a,$is_set==0?$html_pay:'',$first['message_fmt']); $is_set=1;$first['purchased']='0';
		}
	}
}else{
        $first['message_fmt'] = str_replace('[ttPay]','',$first['message_fmt']);
        $first['message_fmt'] = str_replace('[/ttPay]','',$first['message_fmt']);
}

    $gid=isset($user['gid'])?$user['gid']:'0'; $my_p=$group['readp']; $target_p=$thread['readp']; $need_refresh=0;
    
    if(($gid!=1)&& $my_p<$target_p ){ message(-1, jump(lang('dear_p'), http_referer(), 2));die();}
	$preg_login = preg_match_all('/\[ttlogin\](.*?)\[\/ttlogin\]/i',$first['message_fmt'],$array);
    if($preg_login) {
        $array_count = count($array[0]);
        $html_hide='<div class="alert alert-warning" role="alert">'.lang('dear_guest2').'<a href ="/user-login.htm"><i class="icon-user"></i> '.lang('login').'</a> '.lang('or_').' <a href ="/user-create.htm"><i class="icon-flask"></i> '.lang('register').'</a></div>';
        for($i=0;$i<$array_count;$i++){
            $a = $array[0][$i];
            $b = '<div class="alert alert-success" role="alert">'.$array[1][$i].'</div>';
            if($uid)$first['message_fmt'] = str_replace($a,$b,$first['message_fmt']);
            else $first['message_fmt'] = str_replace($a,$html_hide,$first['message_fmt']);
        }
    }
    $preg_reply = preg_match_all('/\[ttreply\](.*?)\[\/ttreply\]/i',$first['message_fmt'],$array);
    if($preg_reply) {
        $array_count = count($array[0]);
        $html_reply ='<div class="alert alert-warning" role="alert">'.lang('dear_reply').'</div>';
        if($uid) $replied=db_find_one('post',array('uid'=>$uid,'tid'=>$thread['tid'])); else $replied=array();
        for($i=0;$i<$array_count;$i++){
            $a = $array[0][$i];
            $b = '<div class="alert alert-success" role="alert">'.$array[1][$i].'</div>';
            if($uid AND $replied)$first['message_fmt'] = str_replace($a,$b,$first['message_fmt']);
            if($uid AND isset($gid) AND $gid==1)$first['message_fmt'] = str_replace($a,$b,$first['message_fmt']);
            
            else {$first['message_fmt'] = str_replace($a,$html_reply,$first['message_fmt']);$need_refresh=1;}
        }
    }
$set = setting_get('tt_read');
if($set&& $set['old']==1) {
    $preg_reply2 = preg_match_all('/\[reply\](.*?)\[\/reply\]/i',$first['message_fmt'],$array2);
    if($preg_reply2) {
        $array2_count = count($array2[0]);
        $html_reply ='<div class="alert alert-warning" role="alert">'.lang('dear_reply').'</div>';
        if($uid) $replied=db_find_one('post',array('uid'=>$uid,'tid'=>$thread['tid'])); else $replied=array();
        for($i=0;$i<$array2_count;$i++){
            $a = $array2[0][$i];
            $b = '<div class="alert alert-success" role="alert">'.$array2[1][$i].'</div>';
            if($uid AND $replied)$first['message_fmt'] = str_replace($a,$b,$first['message_fmt']);
            
            else {$first['message_fmt'] = str_replace($a,$html_reply,$first['message_fmt']);$need_refresh=1;}
        }
    }
}

if(!empty($uid)){
if($user['uid']) {
    $logid = db_find_one('viewlog', array('uid' => $user['uid'], 'tid' => $tid));
    if($logid) {
        db_update('viewlog', array('uid' => $user['uid'], 'tid' => $tid), array('dateline' => $time));
    } else {
        db_insert('viewlog', array('uid' => $user['uid'], 'username' => $user['username'], 'tid' => $tid, 'dateline' => $time));
    }
}
}
$viewlog = kv_get('xn_viewlog');
if($viewlog['days']) {
    $deletetime = $time - $viewlog['days'] * 86400;
    $tablepre = $db->tablepre;
    db_exec('delete from '.$tablepre.'viewlog where dateline <='.$deletetime);
}
$logs = db_find('viewlog', array('tid' => $tid), array('dateline' => -1), 1, $viewlog['maxnum']);
$logs_count = db_count('viewlog', array('tid' => $tid));
	
	include _include(APP_PATH.'view/htm/thread.htm');
}



?>