<?php exit;
$r=db_find_one('post',array('pid'=>$pid));
if($r){
   $message=$r['message'];
$n = preg_match_all("/(?:[^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$message,$result);
if($n>0){
	$message=str_replace('[',' [',$message);
	$message=str_replace(']','] ',$message);
	$newm="\${1}<a href=\"\${2}\" target=\"_blank\" _href=\"\${2}\"><span style=\"color:#FF2300\">\${2}</span></a>";
	$message=preg_replace("/([^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$newm,$message);
	$message=str_replace(' [ ','[',$message);
	$message=str_replace('] ',']',$message);
}
   db_update('post',array('pid'=>$pid),array('message'=>$message,'message_fmt'=>$message));
}