<?php exit;
if(!empty($uid)){
if($user['uid']) {
    $logid = db_find_one('viewlog', array('uid' => $user['uid'], 'tid' => $tid));
    if($logid) {
        db_update('viewlog', array('uid' => $user['uid'], 'tid' => $tid), array('dateline' => $time));
    } else {
        db_insert('viewlog', array('uid' => $user['uid'], 'username' => $user['username'], 'tid' => $tid, 'dateline' => $time));
    }
}
}
$viewlog = kv_get('xn_viewlog');
if($viewlog['days']) {
    $deletetime = $time - $viewlog['days'] * 86400;
    $tablepre = $db->tablepre;
    db_exec('delete from '.$tablepre.'viewlog where dateline <='.$deletetime);
}
$logs = db_find('viewlog', array('tid' => $tid), array('dateline' => -1), 1, $viewlog['maxnum']);
$logs_count = db_count('viewlog', array('tid' => $tid));
