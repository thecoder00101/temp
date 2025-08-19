<?php exit;
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
?>