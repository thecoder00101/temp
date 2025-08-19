<?php
!defined('DEBUG') AND exit('Access Denied.');
$action = param(1);

if($action == 'query'){
    if($method == 'POST'){
        $trade_no = param(2);
        empty($trade_no) AND message(-1, '交易号不能为空！');
        $q = fox_pay_query_trade($trade_no, $fox_alipay_arr);
        $q !== FALSE AND message(0, 'Yes');
        message(-1, 'No');
    }else{
        message(-1, 'Access Denied.');
    }
}
elseif($action == 'notify'){
    if($method == 'POST'){
        fox_pay_log($_POST);
        if(empty($fox_alipay_arr['pay_publickey'])){echo 'error'; exit;}
        $alipayPublicKey = $fox_alipay_arr['pay_publickey'];
        $aliPay = new AlipayService($alipayPublicKey);
        $result = $aliPay->rsaCheck($_POST);
        if($result !== FALSE){
            $r = fox_pay_update_user_money($_POST, $fox_alipay_arr);
            if($r !== FALSE){
                echo 'success'; exit;
            }else{
                echo 'error'; exit;
            }
        }
        echo 'error'; exit;
    }else{
        message(-1, 'Access Denied.');
    }
}else{
     message(-1, 'Access Denied.');
}
?>