<?php

!defined('DEBUG') AND exit('Access Denied.');

if ($method == 'GET') {
    include_once _include(APP_PATH.'plugin/zaesky_links/setting.htm');
} else {
    $lidArr = param('lid',array(0));
    $iconArr = param('icon', array(''));
    $nameArr = param('name',array(''));
    $linkArr = param('link',array(''));
    $rankArr = param('rank',array(''));

    $navlist = nav_list();
    $arrlist = array();
    foreach($lidArr as $k=>$v) {
        $arr = array(
            'lid'=>$k,
            'icon'=>array_value($iconArr, $k),
            'name'=>array_value($nameArr, $k),
            'link'=>array_value($linkArr, $k),
            'rank'=>array_value($rankArr, $k)
        );

        if(!isset($navlist[$k])) {

            nav_create($arr);
        } else {
            nav_update($k, $arr);
        }

    }

    // 删除 / delete
    $deletearr = array_diff_key($navlist, $lidArr);
    foreach($deletearr as $k=>$v) {
//        if(in_array($k, $system_forum)) continue;
        nav_delete($k);

    }

    message(0, lang('save_successfully'));
}
function nav_update($lid,$arr){
    $r = nav__update($lid, $arr);
    return $r;
}
function nav__update($lid,$arr){
    $r = db_update('navlinks', array('lid'=>$lid), $arr);
    return $r;
}
function nav_create($arr){
    $r = nav__create( $arr);
    return $r;
}
function nav__create($arr){
    $r = db_create('navlinks',$arr);
    return $r;
}
function nav_delete($lid) {
    $r = db_delete('navlinks', array('lid'=>$lid));
    return $r;
}
function nav_list(){
    $r = array();
    foreach (db_find('navlinks') as $item){
        $r[$item['lid']] = $item;
    }
    return $r;
}