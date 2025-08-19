<?php


function auto_link_check($url,$str1,$str2){

    $str=http_get($url, $timeout = 5, $times = 3);
    $reg1="/<a .*?>.*?<\/a>/";
    $aarray;//这个存放的就是正则匹配出来的所有《a》标签数组
    preg_match_all($reg1,$str,$aarray);

//     $url = $_SERVER['SERVER_NAME'];
//     $from = 'from='.$uid;
    $status = -1;
    for($i=0;$i<count($aarray[0]);$i++){
        $a_json  = xn_json_encode($aarray[0][$i]);
        if(strpos($a_json,$str1) !== false && strpos($a_json,$str2) !== false){
             $status = 1;
        }
    }

    return  $status;

}
?>