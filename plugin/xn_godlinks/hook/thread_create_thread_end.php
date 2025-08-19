<?php exit;
	$r = db_find_one('post',array('pid'=>$pid));
	if($r){
		$n = preg_match_all("/(?:[^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$r['message'],$result);
		if($n>0){
			$r['message']=str_replace('[ttDown]http','[ttDown]',$r['message']);
			$r['message']=str_replace('[',' [',$r['message']);
			$r['message']=str_replace(']','] ',$r['message']);
			$newm="\${1}<a href=\"\${2}\" target=\"_blank\" _href=\"\${2}\"><span style=\"color:#FF2300\">\${2}</span></a>";
			$r['message']=preg_replace("/([^\"]|^)(https?\:\/\/[^\x{4e00}-\x{9fa5}\"\s<]+)/u",$newm,$r['message']);
			$r['message']=str_replace(' [ ','[',$r['message']);
			$r['message']=str_replace('] ',']',$r['message']);
			$r['message']=str_replace('[ttDown]','[ttDown]http',$r['message']);
			db_update('post',array('pid'=>$pid),array('message'=>$r['message'],'message_fmt'=>$r['message']));
		}
	}