<?php !defined('DEBUG') AND exit('Access Denied.');
$action = param(3);
if(empty($action)) {
    if ($method == 'GET') {//设置页面
        include _include(APP_PATH . 'plugin/zz_iqismart_com_autolink/setting.htm');
    } elseif ($method == "POST") {
      	$position=param('position');
    
        //设置参数
        $kv = array('position'=>$position);
        kv_cache_delete('iqismart_com_autolink');
        kv_set('iqismart_com_autolink', $kv);

        message(0,'设置成功！');
    }
}

 
?>