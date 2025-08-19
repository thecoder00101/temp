<?php
!defined('DEBUG') AND exit('Access Denied.');
if ($method == 'GET') {
    $pconfig = kv_get('nciaer_copyright');
    $showurl= $pconfig['showurl'];
    $title = $pconfig['title'];
    $copyright = $pconfig['copyright'];
    $color = $pconfig['color'];
    $bgcolor = $pconfig['bgcolor'];
    $logo = $pconfig['logo'];
    include _include(APP_PATH . 'plugin/nciaer_copyright/setting.htm');
} else {
    $showurl = param('showurl', 0);
    $title = param('title');
    $copyright = param('copyright', '', FALSE);
    $color = param('color');
    $bgcolor = param('bgcolor');
    $pconfig = array();
    $pconfig['showurl'] = $showurl;
    $pconfig['title'] = $title;
    $pconfig['copyright'] = $copyright;
    $pconfig['color'] = $color;
    $pconfig['bgcolor'] = $bgcolor;
    kv_set('nciaer_copyright', $pconfig);
    message(0, '提交成功！');
}
