<?php
/*
	Xiuno BBS 4.0 图片水印功能
	插件由修罗学院整理 https://xiu.no/
*/
!defined('DEBUG') AND exit('Access Denied.');
function sg_form_multi_checkbox($name, $arr, $checked = array()) {
	$s = '';
	foreach($arr as $k=>$v) {
		$ischecked = array_key_exists($k, $checked);
		$s .= form_checkbox($name."[$k]", $ischecked, $v).' ';
	}
	return $s;
}
if($method == 'GET') {
	$kv = setting_get('xn_watermark');
	$input = array();
	$input['type'] =form_radio('type', array(1=>'图片', 0=>'文字'), $kv['type']);
	$input['position'] = form_radio('position', array(1=>'顶端居左', 2=>'顶端居中', 3=>'顶端居右', 4=>'中部居左', 5=>'中部居中', 6=>'中部居右', 7=>'底端居左', 8=>'底端居中', 9=>'底端居右'), $kv['position']);
	$input['format'] = sg_form_multi_checkbox('format', array('gif'=>'gif', 'jpg'=>'jpg', 'jpeg'=>'jpeg', 'png'=>'png', 'bmp'=>'bmp'), $kv['format']);
	$input['text'] = form_text('text', $kv['text']);
	$input['size'] = form_text('size', $kv['size']);
	$input['color'] = form_text('color', $kv['color']);
	$input['font'] = form_text('font', $kv['font']);
	$input['width'] =form_radio('width', array(0=>'固定', 1=>'比例'), $kv['width']);
	$logo = '../plugin/xn_watermark/img/logo.png?'.$time;
	include _include(APP_PATH.'plugin/xn_watermark/setting.htm');
} else {
	$action = param(3);
	if($action == 'logo') {
		$data = param('data', '', FALSE);
		empty($data) AND message(-1, lang('data_is_empty'));
		$data = base64_decode_file_data($data);
		$size = strlen($data);
		$size > 2048000 AND message(-1, lang('filesize_too_large', array('maxsize'=>'2M', 'size'=>$size)));
		$path = '../plugin/xn_watermark/img/';
		$url = '../plugin/xn_watermark/img/logo.png';
		!is_dir($path) AND (mkdir($path, 0777, TRUE) OR message(-2, lang('directory_create_failed')));
		file_put_contents($path.'logo.png', $data) OR message(-1, lang('write_to_file_failed'));
		message(0, array('url'=>$url));
	} else {
	$kv = array();
	$kv['type'] = param('type', 0);
	$kv['position'] = param('position', 0);
	$kv['format'] = param('format', array(0));
	$kv['text'] = param('text');
	$kv['size'] = param('size', 0);
	$kv['color'] = param('color');
	$kv['font'] = param('font');
	$kv['width'] = param('width', 0);
	setting_set('xn_watermark', $kv);
	message(0, lang('save_successfully'));
	}
}

?>