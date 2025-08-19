<?php
/**
 * 用户签名插件设置文件
 *
 * @create 2020-02-07
 * @author 西部主机论坛 https://www.westping.com
 */
!defined('DEBUG') and exit('Access Denied.');

if ($method == 'GET') {
    $get_signature = kv_get('user_signature');
    include _include(APP_PATH.'plugin/art_signature/view/htm/setting.htm');
} else {
    $get_signature = array();
    $get_signature['position'] = param('position');
    $get_signature['html'] = param('html');
    $get_signature['report'] = param('report');
    $characters = param('characters');
	$message ='';
    if ($characters >= 1 && $characters <= 255) {
        $get_signature['characters'] = param('characters');
    } else {
        $get_signature['characters'] = '120';
		$message = '<p>注意：签名字数取值不正确，已自动设置为120。</p>';
    }
    kv_set('user_signature', $get_signature);
    message(0, '<p>设置成功</p>' .$message.'<a role="button" class="btn btn-secondary btn-block m-t-1" href="javascript:history.back();">返回</a>');
}
