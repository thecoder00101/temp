
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
