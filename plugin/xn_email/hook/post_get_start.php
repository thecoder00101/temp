if(empty($user['email_v']) || $user['email_v'] != '1'){
	message(-1,jump('验证邮箱后才能发帖',url('my-email'),2));die;
}