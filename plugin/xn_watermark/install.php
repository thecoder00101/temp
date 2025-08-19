<?php

/*
	Xiuno BBS 4.0 图片水印功能
	插件由修罗学院整理 https://xiu.no/
*/
!defined('DEBUG') AND exit('Forbidden');
// 初始化
$kv = setting_get('xn_watermark');
if(!$kv) {
	$kv = array('type'=>'1', 'position'=>'9', 'format'=>array('gif'=>1,'jpg'=>1,'jpeg'=>1,'png'=>1), 'text'=>'修罗学院 https://xiu.no/', 'size'=>'16', 'color'=>'#FF0000', 'font'=>'t1.ttf', 'width'=>'0');
	setting_set('xn_watermark', $kv);
}
?>