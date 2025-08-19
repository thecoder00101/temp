<?php
exit;
if ($action == 'signature') {
    if ($method == 'GET') {
        include _include(APP_PATH.'plugin/art_signature/view/htm/signature.htm');
    } elseif ($method == 'POST') {
        $strlimit = $get_signature['characters'];
        if (isset($strlimit) && $strlimit >= 1 && $strlimit <= 255) {
            $strlimit = $strlimit;
        } else {
            $strlimit = "120";
        }
        $my_signature = param('my_signature', '', $htmlspecialchars = false);
        if (!empty($my_signature)) {
            if (xn_strlen($my_signature) > $strlimit) {
                message(0, "不能超过".$strlimit."个字符哦。");
            } else {
                include _include(APP_PATH.'plugin/art_signature/model/xss.php');				
                $my_signature = strip_tags($my_signature, "<a>");
                $my_signature = remove_xss($my_signature);
                $my_signature = htmlspecialchars($my_signature);
                $do = user_update($uid, array('signature' => $my_signature));
                $do === false and message(0, '签名设定失败！');
                message(0, "签名设定成功");
            }
        } else {
            user_update($uid, array('signature' => '')) and message(0, "您没有输入内容，所以前台会显示“懒人签名”。");
        }
    }
}
