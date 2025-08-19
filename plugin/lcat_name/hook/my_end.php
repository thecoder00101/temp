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
}