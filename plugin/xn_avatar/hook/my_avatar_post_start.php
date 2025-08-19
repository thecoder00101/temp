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