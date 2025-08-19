<?php
/*
	Xiuno BBS 4.0 图片水印功能
	插件由修罗学院整理 https://xiu.no/
*/

!defined('DEBUG') AND exit('Forbidden');
$r = setting_delete('xn_watermark');
$r === FALSE AND message(-1, '卸载失败');

?>