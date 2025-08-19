<?php
  $light_config = setting_get('admin_light_setting');
  $main_switch = 'col-lg-7';
  $index_header_switch = 'hidden-lg';
  $icon_switch = 'hidden-sm hidden-md';
  $thread_nav_switch = 'd-none';
  $thread_left_l = 'd-lg-block';
  $thread_left_m = 'col-lg-8';
  if (isset($light_config['side_nav_switch']) && $light_config['side_nav_switch'] == 0) { 
    $main_switch = 'col-lg-9';
    $index_header_switch = '';
    $icon_switch='d-none';
  }
  if (isset($light_config['thread_top_nav']) && $light_config['thread_top_nav'] == 1) { 
    $thread_nav_switch = '';
  }
  if (isset($light_config['thread_left_switch']) && $light_config['thread_left_switch'] == 0) {
    $thread_left_l = '';
    $thread_left_m = 'col-lg-9';
     }

     //数字转换
     function convert($num) {
      if ($num >= 100000){
          $num = round($num / 10000) .'w';
      } 
      else if ($num >= 10000) {
          $num = round($num / 10000, 1) .'w';
      } 
      else if($num >= 1000) {
          $num = round($num / 1000, 1) . 'k';
      }
      return $num;
  }

  //小伙伴们
  function discovery_get_site_new_user($num){
    //先获取用户列表
    $userlist = cache_get('get_site_new_user');
    if(empty($userlist)){
        $userlist = db_find('user', array(), array('login_date'=>-1), 1, $num, 'uid');
        foreach ($userlist as &$user) {
            $username = $user['username'];
            $user['dname'] = $username;
        }
        cache_set('get_site_new_user',$userlist,3600);//有效期1小时
    }
    return $userlist;
  }
  
  //热门板块
  function discovery_get_hot_forum(){
  $hotforumList = cache_get('discovery_get_hot_forum');
  if(empty($hotforumList)){
    $hotforumList = db_find('forum',array(),array('threads'=>-1),1,3);
    cache_set('discovery_get_hot_forum',$hotforumList,86400);//有效期1天
  }
  
  return $hotforumList;
  }
  //财富榜
  function discovery_get_gold_List(){
  $goldRankList = cache_get('discovery_get_gold_List');
  if(empty($goldRankList)){
    $goldRankList = db_find('user',array(),array('golds'=>-1),1,5);
    cache_set('discovery_get_gold_List',$goldRankList,86400);//有效期1天
  }
 
  return $goldRankList;
}
  //贡献榜
  function discovery_get_thread_List(){
  $threadRankList = cache_get('discovery_get_thread_List');
  if(empty($threadRankList)){
    $threadRankList = db_find('user',array(),array('threads'=>-1),1,5);
    cache_set('discovery_get_thread_List',$threadRankList,86400);//有效期1天
  }
 
  return $threadRankList;
  }
  //活跃榜
  function discovery_get_login_List(){
  $loginRankList = cache_get('discovery_get_login_List');
  if(empty($loginRankList)){
    $loginRankList = db_find('user',array(),array('logins'=>-1),1,5);
    cache_set('discovery_get_login_List',$loginRankList,86400);//有效期1天
  }
  return $loginRankList;
}

  //获取回复最多的文章，默认10篇，总排行
  function discovery_get_site_top_list(){
    $result = cache_get('discovery_get_site_top_list');
    if(empty($result)){
      $result = db_find('thread', array('views' =>array('>'=>100),'posts'=>array('>'=>20)), array('views'=>2),1,20);//按views倒序
      cache_set('discovery_get_site_top_list',$result,86400);//有效期1天
    }
    return $result;
  } 

  //获取最新的帖子
  function get_new_threadlist(){
    $newList = db_find('thread',array(),array('create_date'=>-1),1,10);
    return $newList;
  }

    //获取最新回复的帖子
    function get_new_reply_threadlist(){
      $newReplyList = db_find('thread',array(),array('last_date'=>-1),1,10);
      return $newReplyList;
    }
     
  //最新评论
  function discovery_get_comment_list() {
    $cachename = "discovery_get_comment_list";
    $threadlist = cache_get($cachename);
    if($threadlist === NULL) {
      $threadlist = post_find( array( "isfirst" => 0, "quotepid" => 0 ), array( "create_date" => "-1" ), 1, 10 );
      cache_set($cachename, $threadlist, 600);
    }
    return $threadlist;
  }
  

  $arronline=assoc_unique(online_list_cache(),'uid');//获取在线用户信息并且根据uid值去重复

$rew=$conf['url_rewrite_on']==0 ? '?':'';//伪静态

function assoc_unique($arr, $key) {  //根据指定的key值为数组去重
        $tmp_arr = array();  
        foreach($arr as $k => $v) {  
            if(in_array($v[$key], $tmp_arr)) {  
                unset($arr[$k]);  
            } else {  
                $tmp_arr[] = $v[$key];  
            }  
        }  
        sort($arr);  
        return $arr;  
} 
function getlistn(){//返回修罗主程序所处相对目录 
return substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-10);   
}



//给我一个文件夹，返回该文件夹下所有的文件数量
function ShuLiang($url)//造一个方法，给一个参数
{
    $sl=0;//造一个变量，让他默认值为0;
    $arr = glob($url);//把该路径下所有的文件存到一个数组里面;
    foreach ($arr as $v)//循环便利一下，吧数组$arr赋给$v;
    {
        if(is_file($v))//先用个if判断一下这个文件夹下的文件是不是文件，有可能是文件夹;
        {
            $sl++;//如果是文件，数量加一;
        }
        else
        {
            $sl+=ShuLiang($v."/*");//如果是文件夹，那么再调用函数本身获取此文件夹下文件的数量，这种方法称为递归;
        }
    }
    return $sl;//当这个方法走完后，返回一个值$sl,这个值就是该路径下所有的文件数量;
}
?>