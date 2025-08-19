<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);

if($action == 'digest') {
	$page = param(2, 1);
	$pagesize = 20;
	
	$digests = $user['digests'];
	$pagination = pagination(url("user-$uid-{page}-1"), $digests, $page, $pagesize);
	$threadlist = thread_digest_find_by_uid($uid, $page, $pagesize);
	
	include _include(APP_PATH.'plugin/xn_digest/view/htm/my_digest.htm');
}
if($action == 'post') {
	
	
	
	$page = param(2, 1);
	$pagesize = 20;
	
	$totalnum = $user['posts'];
	$pagination = pagination(url("my-post-{page}"), $totalnum, $page, $pagesize);
	$postlist = post_find_by_uid($uid, $page, $pagesize);
	
	post_list_access_filter($postlist, $gid);

	
	
	include _include(APP_PATH.'plugin/xn_mypost/view/htm/my_post.htm');
	
}

$user = user_read($uid);
user_login_check();

$header['mobile_title'] = $user['username'];
$header['mobile_linke'] = url("my");

is_numeric($action) AND $action = '';

$active = $action;



if(empty($action)) {
	
	$header['title'] = lang('my_home');
	
	
	
	include _include(APP_PATH.'view/htm/my.htm');
	
/*	
} elseif($action == 'profile') {
	
	if($ajax) {
		// user_safe_info($user);
		message(0, $user);
	} else {
		include _include(APP_PATH.'view/htm/my_profile.htm');
	}
*/
	
} elseif($action == 'password') {
	
	if($method == 'GET') {
		
		
		
		include _include(APP_PATH.'view/htm/my_password.htm');
		
	} elseif($method == 'POST') {
		
		
		
		$password_old = param('password_old');
		$password_new = param('password_new');
		$password_new_repeat = param('password_new_repeat');
		$password_new_repeat != $password_new AND message(-1, lang('repeat_password_incorrect'));
		md5($password_old.$user['salt']) != $user['password'] AND message('password_old', lang('old_password_incorrect'));
		$password_new = md5($password_new.$user['salt']);
		$r = user_update($uid, array('password'=>$password_new));
		$r === FALSE AND message(-1, lang('password_modify_failed'));
		
		
		message(0, lang('password_modify_successfully'));
		
	}
	

} elseif($action == 'thread') {

	
	
	$page = param(2, 1);
	$pagesize = 20;
	$totalnum = $user['threads'];
	
	
	
	$pagination = pagination(url('my-thread-{page}'), $totalnum, $page, $pagesize);
	$threadlist = mythread_find_by_uid($uid, $page, $pagesize);
	
	
	
	include _include(APP_PATH.'view/htm/my_thread.htm');

	
} elseif($action == 'avatar') {
	
	if($method == 'GET') {
		
		
		
		include _include(APP_PATH.'view/htm/my_avatar.htm');
	
	} else {
		
			$imgsrc = param('imgsrc');
	if($imgsrc) {
			user_update($uid, array('avatar_auto'=>$imgsrc,'avatar'=>0));
			$dir = substr(sprintf("%09d", $uid), 0, 3);
			$avatar_url = $conf['upload_url']."avatar/$dir/$user[uid].png" ;
			isset($avatar_url) AND unlink($avatar_url);
		message(0, '修改头像成功');
	}else{
			user_update($uid, array('avatar_auto'=>'0'));
	}
		
		$width = param('width');
		$height = param('height');
		$data = param('data', '', FALSE);
		
		empty($data) AND message(-1, lang('data_is_empty'));
		$data = base64_decode_file_data($data);
		$size = strlen($data);
		$size > 2048000 AND message(-1, lang('filesize_too_large', array('maxsize'=>'2M', 'size'=>$size)));
		
		$filename = "$uid.png";
		$dir = substr(sprintf("%09d", $uid), 0, 3).'/';
		$path = $conf['upload_path'].'avatar/'.$dir;
		$url = $conf['upload_url'].'avatar/'.$dir.$filename;
		!is_dir($path) AND (mkdir($path, 0777, TRUE) OR message(-2, lang('directory_create_failed')));
		
		
		file_put_contents($path.$filename, $data) OR message(-1, lang('write_to_file_failed'));
		
		user_update($uid, array('avatar'=>$time));
		
		
		
		message(0, array('url'=>$url));
		
	}
}


if ($action == 'signature') {
    if ($method == 'GET') {
        include _include(APP_PATH.'plugin/art_signature/view/htm/signature.htm');
    } elseif ($method == 'POST') {
        $strlimit = $get_signature['characters'];
        if (isset($strlimit) && $strlimit >= 1 && $strlimit <= 255) {
            $strlimit = $strlimit;
        } else {
            $strlimit = "120";
        }
        $my_signature = param('my_signature', '', $htmlspecialchars = false);
        if (!empty($my_signature)) {
            if (xn_strlen($my_signature) > $strlimit) {
                message(0, "不能超过".$strlimit."个字符哦。");
            } else {
                include _include(APP_PATH.'plugin/art_signature/model/xss.php');				
                $my_signature = strip_tags($my_signature, "<a>");
                $my_signature = remove_xss($my_signature);
                $my_signature = htmlspecialchars($my_signature);
                $do = user_update($uid, array('signature' => $my_signature));
                $do === false and message(0, '签名设定失败！');
                message(0, "签名设定成功");
            }
        } else {
            user_update($uid, array('signature' => '')) and message(0, "您没有输入内容，所以前台会显示“懒人签名”。");
        }
    }
}

elseif ($action == 'favorite') {

	$header['title'] = lang('haya_favorite_my_favorite') . " - " . $conf['sitename'];

	$haya_favorite_config = setting_get('haya_favorite');
	
	
	
	if ($method == 'GET') {
		
		
		$pagesize = intval($haya_favorite_config['user_favorite']);
		$page = param(2, 1);
		$cond['uid'] = $uid; 
		
		$haya_favorite_count = haya_favorite_count($cond);
		$threadlist = haya_favorite_find($cond, array('create_date' => -1), $page, $pagesize);
		$pagination = pagination(url("my-favorite-{page}"), $haya_favorite_count, $page, $pagesize);
		
		
		
		include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite.htm');

	} else {

		$action = param(2, 'add');
		$tid = param('tid');
		if (!$user) {
			message(0, lang('haya_favorite_user_favorite_error_tip'));
		}

		$thread = thread_read($tid);
		empty($thread) AND message(0, lang('thread_not_exists'));
		$haya_check_favorite = haya_favorite_find_by_uid_and_tid($uid, $tid);
		
		$haya_favorite_user_favorite_count = isset($haya_favorite_config['user_favorite_count']) ? intval($haya_favorite_config['user_favorite_count']) : 20;
		
		
		
		if ($action == 'create') {
			
			
			if (!empty($haya_check_favorite)) {
				message(0, lang('haya_favorite_user_have_favorite_tip'));
			}
			
			haya_favorite_create(array(
				'tid' => $tid, 
				'uid' => $user['uid'],
				'create_date' => time(),
				'create_ip' => $longip,
			));
			
			if (isset($haya_favorite_config['favorite_count_type']) 
				&& $haya_favorite_config['favorite_count_type'] == 1
			) {
				$haya_favorite_count = haya_favorite_count(array('tid' => $tid));
				
				thread__update($tid, array('favorites' => $haya_favorite_count));
			} else {
				$haya_favorite_count = $thread['favorites'] + 1;
				
				haya_favorite_thread_user_favorites($tid, 1);
			}
			
			// 更新当前用户收藏数
			$haya_favorite_user_now_favorite_count = haya_favorite_count(array('uid' => $uid));
			user__update($uid, array('favorites' => $haya_favorite_user_now_favorite_count));

			$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
			ob_start();
			include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
			$haya_favorite_user_html = ob_get_clean();
			
			$haya_favorite_msg = array(
				'count' => $haya_favorite_count,
				'users' => $haya_favorite_user_html,
				'msg' => lang('haya_favorite_user_favorite_success_tip'),
			);
			
			

if (function_exists("notice_send")) {
	
	
	
	$thread = thread_read($thread['tid']);
	$thread['subject'] = notice_substr($thread['subject'], 20);
	
	$notice_thread_subject = $thread['subject'];
	$notice_thread_substr_subject = htmlspecialchars(strip_tags($thread['subject']));
	$notice_thread_substr_subject = notice_substr($notice_thread_substr_subject, 20);
	$notice_thread_url = url('thread-'.$thread['tid']);
	$notice_thread = '<a target="_blank" href="'.$notice_thread_url.'">《'.$notice_thread_subject.'》</a>';
	
	$notice_user_url = url('user-'.$user['uid']);
	$notice_user_avatar_url = $user['avatar_url'];
	$notice_user_username = $user['username'];
	$notice_user = '<a href="'.$notice_user_url.'" target="_blank"><img class="avatar-1" src="'.$notice_user_avatar_url.'"> '.$notice_user_username.'</a>';
	
	
	
	$notice_msg = str_replace(
		array(
			'{thread_subject}', '{thread_substr_subject}', '{thread_url}', '{thread}', 
			'{user_url}', '{user_avatar_url}', '{user_username}', '{user}'
		),
		array(
			$notice_thread_subject, $notice_thread_substr_subject, $notice_thread_url, $notice_thread, 
			$notice_user_url, $notice_user_avatar_url, $notice_user_username, $notice_user
		),
		lang('haya_favorite_send_notice_for_thread')
	);
	notice_send($user['uid'], $thread['uid'], $notice_msg, 155);

					
}


			
			message(1, $haya_favorite_msg);
		} elseif ($action == 'delete') {
			
			
			if (empty($haya_check_favorite)) {
				message(0, lang('haya_favorite_user_no_favorite_error_tip'));
			}
			
			haya_favorite_delete_by_tid_and_uid($tid, $user['uid']);
			
			if (isset($haya_favorite_config['favorite_count_type']) 
				&& $haya_favorite_config['favorite_count_type'] == 1
			) {
				$haya_favorite_count = haya_favorite_count(array('tid' => $tid));
				
				thread__update($tid, array('favorites' => $haya_favorite_count));
			} else {
				$haya_favorite_count = MAX(0, $thread['favorites'] - 1);
				
				haya_favorite_thread_user_favorites($tid, -1);
			}

			// 更新当前用户收藏数
			$haya_favorite_user_now_favorite_count = haya_favorite_count(array('uid' => $uid));
			user__update($uid, array('favorites' => $haya_favorite_user_now_favorite_count));
			
			$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
			ob_start();
			include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
			$haya_favorite_user_html = ob_get_clean();
			
			$haya_favorite_msg = array(
				'count' => $haya_favorite_count,
				'users' => $haya_favorite_user_html,
				'msg' => lang('haya_favorite_user_delete_favorite_success_tip'),
			);
			
			
			
			message(1, $haya_favorite_msg);
		}
		
	}

} elseif ($action == 'favorites') {
	
	$header['title'] = lang('haya_favorite_my_favorite') . " - " . $conf['sitename'];
	
	$haya_favorite_config = setting_get('haya_favorite');
	
	if (strtolower($haya_favorite_config['user_favorite_sort']) == 'asc') {
		$user_favorite_sort = 'asc';
	} else {
		$user_favorite_sort = 'desc';
	}
	
	$orderby = param('orderby', $user_favorite_sort);
	if (strtolower($orderby) == 'asc') {
		$orderby_config = array('create_date' => 1);
	} else {
		$orderby_config = array('create_date' => -1);
	}
	
	$pagesize = intval($haya_favorite_config['user_favorite']);
	$page = param(2, 1);
	$cond['uid'] = $uid; 
	
	$haya_favorite_count = haya_favorite_count($cond);
	$threadlist = haya_favorite_find($cond, $orderby_config, $page, $pagesize);
	$pagination = pagination(url("my-favorites-{page}", array("orderby" => $orderby)), $haya_favorite_count, $page, $pagesize);
	
	include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorites.htm');	
}




elseif ($action == 'follow') {
	$action2 = param(2, 'timeline');
	$my_follow_site = ' - 关注';
	
	$haya_follow_config = setting_get('haya_follow');
	
	if ($action2 == 'follows') {
		$header['title'] = '我关注的' . $my_follow_site;
		
		$pagesize = intval($haya_follow_config['follow_user_pagesize']);
		$page = param(3, 1);

		$haya_follow_count = haya_follow_count(array('follow_uid' => $uid));
		$haya_follow_follows = haya_follow_find(array('follow_uid' => $uid), array('create_date' => -1), $page, $pagesize);
		$haya_follow_pagination = pagination(url("user-fans-{page}"), $haya_follow_count, $page, $pagesize);

		include _include(APP_PATH.'plugin/haya_follow/view/htm/my_follow_follows.htm');	
	} elseif ($action2 == 'fans') {
		$header['title'] = '关注我的' . $my_follow_site;
		
		$pagesize = intval($haya_follow_config['follow_user_pagesize']);
		$page = param(3, 1);

		$haya_follow_count = haya_follow_count(array('uid' => $uid));
		$haya_follow_followeds = haya_follow_find(array('uid' => $uid), array('create_date' => -1), $page, $pagesize);
		$haya_follow_pagination = pagination(url("user-fans-{page}"), $haya_follow_count, $page, $pagesize);

		include _include(APP_PATH.'plugin/haya_follow/view/htm/my_follow_fans.htm');	
	} else {
		$header['title'] = '时间线' . $my_follow_site;
		
		$action3 = param(3, 'all');
		
		$pagesize = intval($haya_follow_config['timeline_post_pagesize']);
		$page = param(4, 1);
		
		$haya_follow_life_time = intval($haya_follow_config['followed_life_time']);
		$haya_follow_my_follow_user_count = haya_follow_count_cache(array('follow_uid' => $uid, 'show_dynamic' => 1), $haya_follow_life_time);
		$haya_follow_my_follow_uids = haya_follow_find_uids_by_follow_uid_cache($uid, $haya_follow_my_follow_user_count, $haya_follow_life_time);
		$haya_follow_my_follow_uids[] = $uid;		
		
		$action3 = strtolower($action3);
		if ($action3 == 'thread') {
			$follow_where = array('uid' => $haya_follow_my_follow_uids, 'isfirst' => 1);
		} elseif ($action3 == 'post') {
			$follow_where = array('uid' => $haya_follow_my_follow_uids, 'isfirst' => 0);
		} else {
			$follow_where = array('uid' => $haya_follow_my_follow_uids);
			$action3 = 'all';
		}
		
		$haya_follow_user_post_count = post_count($follow_where);
		$haya_follow_user_post_list = post_find($follow_where, array('pid' => -1), $page, $pagesize);
		$haya_follow_user_post_pagination = pagination(url("my-follow-timeline-{$action3}-{page}"), $haya_follow_user_post_count, $page, $pagesize);		
		
		$haya_follow_my_follow_user_remarks_uids = array();
		if (!empty($haya_follow_user_post_list)) {
			foreach ($haya_follow_user_post_list as & $haya_follow_user_post) {
				$haya_follow_user_post['thread'] = thread_read_cache($haya_follow_user_post['tid']);
				
				if (!in_array($haya_follow_user_post['uid'], $haya_follow_my_follow_user_remarks_uids)) {
					$haya_follow_my_follow_user_remarks_uids[] = $haya_follow_user_post['uid'];
				}
			}
		}
		
		$haya_follow_my_follow_user_list = haya_follow__find(array('follow_uid' => $uid, 'uid' => $haya_follow_my_follow_user_remarks_uids), array('create_date' => -1), 1, count($haya_follow_my_follow_user_remarks_uids));
		
		$haya_follow_my_follow_user_remarks_list = array();
		
		if (!empty($haya_follow_my_follow_user_list)) {
			foreach ($haya_follow_my_follow_user_list as $haya_follow_my_follow_user) {
				$haya_follow_my_follow_user_remarks_list[$haya_follow_my_follow_user['uid']] = $haya_follow_my_follow_user;
			}
		}
	
		include _include(APP_PATH.'plugin/haya_follow/view/htm/my_follow_timeline.htm');	
	}
	
} 



elseif ($action == 'post_like') {
	
	if (isset($haya_post_like_config['open_my_post_like'])
		&& $haya_post_like_config['open_my_post_like'] != 1
	) {
		message(-1, lang('haya_post_like_my_no_post_like_tip'));
	}

	$pagesize = intval($haya_post_like_config['my_post_like_pagesize']);
	$page = param(2, 1);
	$cond['uid'] = $uid; 
	
	$haya_post_like_count = haya_post_like_count($cond);
	$haya_post_like_post_list = haya_post_like_find($cond, array('create_date' => -1), $page, $pagesize);
	if (!empty($haya_post_like_post_list)) {
		foreach ($haya_post_like_post_list as & $haya_post_like_post_value) {
			$haya_post_like_post_value['thread'] = thread_read_cache($haya_post_like_post_value['tid']);
		}
	}
	
	$pagination = pagination(url("my-post_like-{page}"), $haya_post_like_count, $page, $pagesize);
	
	include _include(APP_PATH.'plugin/haya_post_like/view/htm/my_post_like.htm');
}




elseif($action == 'notice') {

	if($method == 'GET') {

		$page = param(3, 1);
		$pagesize = 20;
		$active = 'notice';
		$notices = $user['notices'];
		$type = param(2, 0);

		$notice_menu = include _include(APP_PATH.'plugin/huux_notice/conf/notice_menu.conf.php');
		
		$noticelist = notice_find_by_recvuid($uid, $page, $pagesize, $type);
		$type != 0 AND $notices = notice_count(array('recvuid'=>$uid, 'type'=>$type));

		$pagination = pagination(url("my-notice-$type-{page}"), $notices, $page, $pagesize);
		
		$header['title'] = lang('notice');
		$header['mobile_title'] = lang('notice');

		include _include(APP_PATH.'plugin/huux_notice/view/htm/my_notice.htm');

	} elseif($method == 'POST') {
		$act = param('act');
		if($act == 'readall') {
			// 全部已读
		   	$r = notice_update_by_recvuid($uid, array('isread'=>1));
	    	$r === FALSE AND message(-1, lang('notice_my_update_failed'));
	    	message(0, array('a' => lang('notice_my_update_readed'),'b' => lang('notice_my_update_allread')));
		    
		} elseif($act == 'readone') {
           	// 设置已读
			$nid = param('nid');
			$notice = notice__read($nid);
			$notice['isread'] == 1 AND message(-1, lang('notice_my_update_readed'));
			$notice['recvuid'] != $uid AND message(-1, lang('notice_my_error'));

			$r = notice_update($nid, array('isread'=>1));

			$r === FALSE AND message(-1, lang('notice_my_update_failed'));
			message(0, lang('notice_my_update_readed'));

		} elseif($act == 'delete') {
			// 单条删除
			$nid = param('nid');
			$notice = notice__read($nid);
			$notice['recvuid'] != $uid AND message(-1, lang('notice_my_error'));

			$r = notice_delete($nid);
			$r === FALSE AND message(-1, lang('notice_my_update_failed'));
			message(0, lang('notice_delete_notice_sucessfully'));

		} elseif($act == 'deletearr') {
			// 多条删除
			$nidarr = param('nidarr', array(0));
			empty($nidarr) AND message(-1, lang('notice_my_error'));

			$noticelist = notice_find_by_nids($nidarr);

			foreach($noticelist as &$notice) {
				$nid = $notice['nid'];
				$recvuid = $notice['recvuid'];
				$uid == $recvuid AND notice_delete($nid);
			}
			message(0, lang('notice_delete_notice_sucessfully'));

		} else {
			// 清空所有暂时不添加
			message(-1, lang('notice_my_error'));

		}
	}	
}
elseif($action == 'Hon'){
	$uid = param(2, 0);
	$method != 'POST' AND message(-1, 'Method error');
	$u = user_read($uid);
	
	$times = time();
	$one_times = strtotime("-1 month");
	
	$r = db_update('user', array('uid'=>$uid), array('one_times'=>$one_times,'times'=>$times));
	
	$r === FALSE AND message(-1, lang('update_failed'));
	message(0, lang('update_successfully'));
	
}elseif($action == 'name') {
$nowtime=time();
$time=$nowtime-$user['create_date'];
$day=intval($time/86400);
include _include(APP_PATH.'plugin/lcat_name/htm/my_name.htm');
}elseif($action == 'namerank'){
$ranklist=db_find('user',array('uid'=>array('!='=>0)),array('uid'=>1));
include _include(APP_PATH.'plugin/lcat_name/htm/my_namerank.htm');
}elseif($action == 'credits') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_credits/view/htm/my_credits.htm');
}elseif($action == 'purchased') {
    if($method == 'GET'){
        $pagesize = 20;
        $page = param(2, 1);
        $cond = array('uid'=>$uid);
        $threadlist = credits_thread_purchased_find_by_uid($uid, $page, $pagesize);
        $pagination = pagination(url("my-purchased-{page}"), credits_purchased_count($cond), $page, $pagesize);
        include _include(APP_PATH.'plugin/tt_credits/view/htm/my_purchased.htm');
    }
}elseif($action == 'trade') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_credits/view/htm/my_trade.htm');
    elseif($method=='POST'){
        $op = param('op');
        if($op=='n'){
            $set=setting_get('tt_credits');$e_rmb = param('e_rmb'); $my_rmbs=$user['rmbs'];$my_golds=$user['golds'];$min=$set['min'];$e_rmb_raw=$e_rmb;
            $e_rmb *= $set['exchange_n'];
            if($e_rmb<$min) {message(-1, '最低兑换金额：¥'.($min).'，您兑换的金额不足。');die();}
            if($e_rmb<=0 ) {message(-1, lang('ERROR'));die();}
            preg_replace('/[^0-9-]+/','',$e_rmb);
            if($my_rmbs<$e_rmb) {message(-1, lang('credit_no_enough'));die();}
            if(empty($uid)||empty($e_rmb)){message(-1, "ERROR");die();}
            $recent_query = db_find_one('user_pay',array('uid'=>$uid,'type'=>'6'),array('time'=>-1));
            $now_time = time();
            if($now_time-$recent_query['time']<=600) {message(-1, "每10分钟只能兑换一次，您兑换过于频繁！");die();}
            $my_golds+= $e_rmb_raw;
            $my_rmbs -= $e_rmb;
            db_insert('user_pay',array('uid'=>$uid,'status'=>1,'num'=>$e_rmb,'type'=>'6','credit_type'=>'3','code'=>'','time'=>time()));
            user_update($user['uid'],array('rmbs'=>$my_rmbs,'golds'=>$my_golds));
            user_update_group($user['uid']);
            message(0, lang('update_successfully'));
        }elseif($op=='c'){
            $set=setting_get('tt_credits');$e_golds=param('e_golds_c'); $my_golds = $user['golds'];$my_rmbs=$user['rmbs'];$min=$set['min'];$e_golds_raw=$e_golds;
            $e_golds*= $set['exchange_c'];
            if(empty($uid)||empty($e_golds)){message(-1, "ERROR");die();}
            if($e_golds<$min*$set['exchange_c']) {message(-1, '最低兑换金币：'.($min*$set['exchange_c']).'，您兑换的金额不足。');die();}
            if($e_golds<=0 ){message(-1, lang('ERROR'));die();}
            preg_replace('/[^0-9-]+/','',$e_golds);
            if($my_golds<$e_golds){message(-1, lang('credit_no_enough'));die();}
            $recent_query = db_find_one('user_pay',array('uid'=>$uid,'type'=>'6'),array('time'=>-1));
            $now_time = time();
            if($now_time-$recent_query['time']<=600) {message(-1, "每10分钟只能兑换一次，您兑换过于频繁！");die();}
            $my_golds-=$e_golds;
            $my_rmbs+=$e_golds_raw;
            db_insert('user_pay',array('uid'=>$uid,'status'=>1,'num'=>$e_golds,'type'=>'6','credit_type'=>'2','code'=>'','time'=>time()));
            user_update($user['uid'],array('rmbs'=>$my_rmbs,'golds'=>$my_golds));
            user_update_group($user['uid']);
            message(0, lang('update_successfully'));
        }elseif($op=='t'){
            $to_username=param('trans_username'); $to_num=param('trans_num'); $credits_type=param('trans_credits');
            if($to_num<=0) {message(-1, "请输入正数!");die();}
            if(empty($to_username)) {message(-1, "用户名不能为空!");die();}
            if(empty($credits_type)) {message(-1, "积分段为空!");die();}
            if(db_count('user',array('username'=>$to_username))<=0){message(-1, "用户不存在!");die();}
            if($user['username']==$to_username) {message(-1, "不能自己给自己转账！");die();}
            $credits_name =get_credits_name_by_type($credits_type);
            $to_user = db_find_one('user',array('username'=>$to_username));
            if($user[$credits_name]<$to_num) {message(-1, "您的余额不足,请充值!");die();}
            db_update('user',array('username'=>$to_username),array($credits_name.'+'=>$to_num));
            db_update('user',array('uid'=>$uid),array($credits_name.'-'=>$to_num));
            db_insert('user_pay',array('uid'=>$to_user['uid'],'status'=>1,'num'=>$to_num,'type'=>13,'credit_type'=>$credits_type,'time'=>time(),'code'=>''));
            db_insert('user_pay',array('uid'=>$uid,'status'=>1,'num'=>$to_num,'type'=>12,'credit_type'=>$credits_type,'time'=>time(),'code'=>''));
            message(0,'转账成功!');
        }
    }
}
elseif($action == 'record') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_credits/view/htm/my_record.htm');
}elseif($action == 'ranklist') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_ranklist/view/htm/my_ranklist.htm');
} elseif($action=='ranklist_posts') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_ranklist/view/htm/my_ranklist_posts.htm');
} elseif($action=='ranklist_credits') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_ranklist/view/htm/my_ranklist_credits.htm');
} elseif($action=='ranklist_golds') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_ranklist/view/htm/my_ranklist_golds.htm');
} elseif($action=='ranklist_rmbs') {
    if($method == 'GET')
        include _include(APP_PATH.'plugin/tt_ranklist/view/htm/my_ranklist_rmbs.htm');
}
elseif($action == 'sign') {
    if($method == 'POST') {
        $set = setting_get('tt_sign'); $msg2='';$update_list = array('credits+'=>0,'golds+'=>0,'rmbs+'=>0);
        if(empty($uid)){message(-1,"拉取用户信息失败");die();}
        $today0 = strtotime(date('Y-m-d',time()))-1;
        $user_signed = db_count('sign',array('uid'=>$uid,'time'=>array('>'=>$today0)));
        if($user_signed ) {message(-1,"您已经签到过了！");die();}
        $signed = db_count('sign',array('time'=>array('>'=>$today0)));
        if($signed===FALSE) $signed=0;
        $msg2 .= '您是第'.($signed+1).'名签到！<br>';
        if($signed==0){ $update_list['credits+']+= $set['first_credits'];$update_list['golds+']+= $set['first_golds'];$msg2.='[首签奖励]';}
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'))-1;
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $signed_yesterday = db_count('sign',array('uid'=>$uid,'time'=>array('>'=>$beginYesterday,'<'=>$endYesterday)));
        if($signed_yesterday){ $update_list['credits+']+= $set['con_credits'];$update_list['golds+']+= $set['con_golds'];$msg2.='[连签奖励]';}
        $update_list['credits+']+= $set['credits_from']==$set['credits_to']? $set['credits_to']: mt_rand($set['credits_from'],$set['credits_to']);
        $update_list['golds+']+= $set['golds_from']==$set['golds_to']? $set['golds_to']: mt_rand($set['golds_from'],$set['golds_to']);
        if(date('d')==1)
        {$beginLastmonth = strtotime(date('Y-m-01', strtotime('-1 month')))-1; $endLastmonth = strtotime(date('Y-m-t', strtotime('-1 month')))+1;
            $days = date('t', strtotime('-1 month'));$_sql_month = db_count('sign',array('uid'=>$uid,'time'=>array('>'=> $beginLastmonth, '<'=>$endLastmonth)));if($_sql_month===FALSE) $_sql_month=0;
            if($_sql_month==$days) {$msg2.='[月满勤奖励]';$update_list['credits+']+= $set['month_credits'];$update_list['golds+']+= $set['month_golds'];}}
        if(date('w')==6)
        {
            $beginWeek = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w"),date("Y"))))-1;
            $endWeek = strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+6,date("Y"))))+1;
            $_sql_week = db_count('sign',array('uid'=>$uid,'time'=>array('>'=> $beginWeek, '<'=>$endWeek)));
            if($_sql_week===FALSE) $_sql_week=0;
            if($_sql_week==7) {$msg2.='[周满勤奖励]';$update_list['credits+']+= $set['week_credits'];$update_list['golds+']+= $set['week_golds'];}
        }
        db_insert('sign',array('uid'=>$uid,'credits'=>$update_list['credits+'],'golds'=>$update_list['golds+'],'rmbs'=>$update_list['rmbs+'],'time'=>time()));
        if(isset($update_list['credits+'])) $msg[lang('credits1')] = $update_list['credits+'];
        if(isset($update_list['golds+'])) $msg[lang('credits2')] = $update_list['golds+'];
        $msg1='';$flag=0;
        foreach($msg as $k=>$v) {if($flag==1)$msg1.='、';$msg1.=$k.':'.$v;$flag=1;}
        $last_login_date = $user['login_date'];
        $update_list['login_date']=time();
        user_update($uid,$update_list);
        
        message('0','签到成功！'.$msg2.'<br>'.$msg1);
    }
}
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
//添加个人中心路径
if($action == 'autolink') {
	if($method == 'GET') {
	    $act=param('act');
	    if($act == 'add'){
	         $link = 	db_find_one('autolink',array('uid'=>$uid)  ) ;
	         //
            $url = "";
            include _include(APP_PATH.'plugin/zz_iqismart_com_autolink/view/htm/autolink.htm');
	    }

	    if($act =='down'){
	        if($gid !=1 ){
	            message(-1,"无权限");
	        }else{
	            $id=param('id');
	            db_update('autolink',array('id'=>$id),array('status'=>-1));
	            cache_delete('autolinks');
	            message(0,'下线成功！');
	        }
	    }

	} elseif($method == 'POST') {
        $siteTitle = param('siteTitle');
        $siteUrl = param('siteUrl');
        $siteDesc = param('siteDesc');
        if(empty($siteTitle)) message(-1, '请填写网站名称');
        if(empty($siteUrl)) message(-1, '请填写网站主页链接');
        if(empty($siteDesc)) message(-1, '请填写网站简介');

        if($gid == 1){
             db_create('autolink', array('uid'=>0, 'siteTitle'=>$siteTitle,'siteUrl'=>$siteUrl,'siteDesc'=>$siteDesc,'create_time'=>time(),'status'=>1));
             cache_delete('autolinks');
            message(0, '恭喜！您的链接已添加本站首页，请刷新首页查看');
            return;
        }

		// 查询用户是否有记录
		$link = db_find_one('autolink',array('uid'=>$uid));
		if(empty($link)){
		    db_create('autolink', array('uid'=>$uid, 'siteTitle'=>$siteTitle,'siteUrl'=>$siteUrl,'siteDesc'=>$siteDesc,'create_time'=>time(),'status'=>0));
		}else{
		    db_update('autolink',array('uid'=>$uid),array('siteTitle'=>$siteTitle,'siteUrl'=>$siteUrl,'siteDesc'=>$siteDesc,'create_time'=>time(),'status'=>0));
		}

       $str1 = $_SERVER['SERVER_NAME'];
       $str2 = 'from='.$uid;
       $status = auto_link_check($siteUrl,$str1,$str2);

       if($status == 1){
         db_update('autolink',array('uid'=>$uid),array('status'=>1));
         cache_delete('autolinks');
         message(0, '恭喜！您的链接已添加本站首页，请刷新首页查看');
       }else{
         message(-1, '您的网站检测不到本站链接，请检查');
       }





	}
}


?>