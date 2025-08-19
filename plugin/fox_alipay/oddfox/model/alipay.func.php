<?php
function fox_pay_orderid(){
    $orderid = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    return $orderid;
}
function fox_pay_trade_no_fmt($trade_no, $status = 0){
    switch((int)$status){
        case 0:  $str = !empty($trade_no) ? $trade_no : '暂无';  break;
        case 1:  $str = !empty($trade_no) ? $trade_no : '支付超时';  break;
        case 2:  $str = $trade_no; break;
        case 3:  $str = !empty($trade_no) ? $trade_no : '已扫码，付款中...';  break;
        case 4:  $str = !empty($trade_no) ? $trade_no : '已全额退款';  break;
        default: $str = !empty($trade_no) ? $trade_no : '未扫码';  break;
    }
    return $str;
}
function fox_pay_status_fmt($status = 0){
    switch((int)$status){
        case 0:  $str = '<span class="text-danger">未支付</span>'; break;
        case 1:  $str = '<span class="text-danger">已关闭</span>';  break;
        case 2:  $str = '<span class="text-success">已付款</span>'; break;
        case 3:  $str = '<span class="text-primary">待付款</span>'; break;
        case 4:  $str = '<span class="text-primary">已退款</span>'; break;
        default: $str = '<span class="text-warning">未扫码</span>'; break;
    }
    return $str;
}
function fox_pay_mypay_post($user, $money = 0, $arr = array()){
    global $db;
    $money_fmt = sprintf("%.2f", $money);
    ($money_fmt < 0.01) AND message('amount', '金额不能小于0.01元');
    $appid = $arr['pay_appid'];
    $rsaPrivateKey = $arr['pay_privatekey'];
    empty($appid) AND message(-1, '支付宝APPID不能为空！');
    empty($rsaPrivateKey) AND message(-1, '开
私钥不能为空！');
    $notifyUrl = http_url_path() . url("pay-notify");
    $outTradeNo = fox_pay_userpay($user['uid'], $money_fmt, $arr);
    if(!empty($outTradeNo)){
        $payAmount = !empty($arr['pay_debug']) ? 0.01 : $money_fmt;
        $orderName = $user['username'];
        $aliPay = new AlipayService(false, $arr['pay_timeout']);
        $aliPay->setAppid($appid);
        $aliPay->setNotifyUrl($notifyUrl);
        $aliPay->setRsaPrivateKey($rsaPrivateKey);
        $aliPay->setTotalFee($payAmount);
        $aliPay->setOutTradeNo($outTradeNo);
        $aliPay->setOrderName($orderName);
        $result = $aliPay->doPay();
        $result = $result['alipay_trade_precreate_response'];
        if($result['code'] && $result['code'] == '10000' && $result['msg'] && $result['msg'] == 'Success'){
            message(0, array('url'=>$result['qr_code'], 'msg'=>'请使用支付宝扫码付款！', 'trade_no'=>$result['out_trade_no']));
        }else{
            fox_pay_log($result['msg'].' : '.$result['sub_msg']);
            message(1, array('url'=>'plugin/fox_alipay/oddfox/static/img/err_qr.png', 'msg'=>'二维码获取失败，即将为您刷新页面！'));
        }
    }else{
        message(1, array('url'=>'plugin/fox_alipay/oddfox/static/img/err_qr.png', 'msg'=>'二维码获取失败，即将为您刷新页面！'));
    }
}
function fox_pay_query_trade($outTradeNo, $arr = array()){
    $tradeNo = '';
    if(empty($arr['pay_appid'])) return FALSE;
    if(empty($arr['pay_privatekey'])) return FALSE;
    $aliPay = new AlipayService();
    $aliPay->setAppid($arr['pay_appid']);
    $aliPay->setRsaPrivateKey($arr['pay_privatekey']);
    $aliPay->setOutTradeNo($outTradeNo);
    $aliPay->setTradeNo($tradeNo);
    $result = $aliPay->doQuery();
    $result = $result['alipay_trade_query_response'];
    if($result['code'] && $result['code'] == '10000' && $result['trade_status']){
        $trade_no = !empty($result['trade_no']) ? $result['trade_no'] : '';
        if($result['trade_status'] == "WAIT_BUYER_PAY"){
            db_update('user_foxpay', array('orderid'=>$outTradeNo), array('status'=>3, 'trade_no'=>$trade_no));
            return FALSE;
        }
        if($result['trade_status'] == "TRADE_CLOSED"){
            db_update('user_foxpay', array('orderid'=>$outTradeNo), array('status'=>1, 'trade_no'=>$trade_no));
            return TRUE;
        }
        if($result['trade_status'] == "TRADE_SUCCESS" || $result['trade_status'] == 'TRADE_FINISHED'){
            return TRUE;
        }
    }
    return FALSE;
}
function fox_pay_autoExecute($table, $arr = array()){
    global $db;
    $errno = 0;
    $r = db_create($table, $arr);
    if($r === FALSE){
        $errno = $db->errno;
        if($errno == 1062){
            $arr['orderid'] = fox_pay_orderid();
            $r = fox_pay_autoExecute($table, $arr);
        }
    }else{
        return $arr['orderid'];
    }
}
function fox_pay_userpay($uid, $pay_money, $arr = array()){
    if(!empty($uid)){
        global $conf, $time, $longip;
        $orderid = fox_pay_orderid();
        $pay_create = array(
            'uid' => $uid,
            'money' => $pay_money,
            'rmbs' => (int)$pay_money * (int)$arr['pay_ratio'], //给用户增加rmbs积分
            'orderid' => $orderid,
            'pay_type' => 1,
            'status' => 0,
            'paycode' => 1,
            'paymethod' => '支付宝',
            'create_date' => $time,
        );
        $orderid = fox_pay_autoExecute('user_foxpay', $pay_create);
        fox_pay_add_foxlog($uid, $orderid, $pay_create['rmbs'], 0, 1); //给积分插件传值
        return $orderid;
    }
    return FALSE;
}
function fox_pay_update_user_money($data, $arr = array()){
    if(empty($data)) return FALSE;
    
    if(empty($arr['pay_debug'])){
        $query = db_find_one('user_foxpay', array('orderid'=>$data['out_trade_no'], 'money'=>$data['total_amount']), array('id'=>-1)); //正式模式
    }else{
        $query = db_find_one('user_foxpay', array('orderid'=>$data['out_trade_no']), array('id'=>-1));
    }
    
    if(empty($query)) return FALSE;
    
    if(empty($arr['pay_debug'])){
        if($data['total_amount'] != $query['money']) return FALSE; //正式模式
    }
    if(!empty($arr['pay_email'])){
        if($data['seller_email'] != $arr['pay_email']) return FALSE;
    }
    if($data['trade_status'] != 'TRADE_SUCCESS') return FALSE;
    
    if(!empty($query)){
        if($query['status'] == 3 || $query['status'] == 0){
            $pay_date = strtotime($data['gmt_payment']);
            $trade_no = $data['trade_no'];
            db_update('user_foxpay', array('orderid'=>$data['out_trade_no']), array('status'=>2, 'trade_no'=>$trade_no, 'pay_date'=>$pay_date));
            db_update('user', array('uid'=>$query['uid']), array('rmbs+'=>$query['rmbs']));
            if(function_exists("fox_score_code_fmt")){
                db_update('user_foxlog', array('uid'=>$query['uid'], 'orderid'=>$data['out_trade_no']), array('state'=>2)); //给积分插件传值
            }
            return TRUE;
        }else{
            return TRUE;
        }
    }
    return FALSE;
}
function fox_pay_trade_close($outTradeNo, $arr = array()){
    $query_foxpay = db_find_one('user_foxpay', array('orderid'=>$outTradeNo, 'pay_type'=>1, 'status'=>3));
    empty($query_foxpay) AND message(-1, '该订单不符合关闭条件！');
    $tradeNo = '';
    $appid = $arr['pay_appid'];
    $rsaPrivateKey = $arr['pay_privatekey'];
    empty($appid) AND message(-1, '支付宝开放平台APPID不能为空！');
    empty($rsaPrivateKey) AND message(-1, '开放平台开发助手私钥不能为空！');
    $aliPay = new AlipayService();
    $aliPay->setAppid($appid);
    $aliPay->setRsaPrivateKey($rsaPrivateKey);
    $aliPay->setOutTradeNo($outTradeNo);
    $aliPay->setTradeNo($tradeNo);
    $result = $aliPay->doClose();
    $result = $result['alipay_trade_close_response'];
    if($result['code'] && $result['code'] == '10000'){
        db_update('user_foxpay', array('orderid'=>$outTradeNo), array('status'=>1));
        message(0, '订单已关闭！');
    }else{
        message(-1, $result['sub_msg']);
    }
}
function fox_pay_trade_refund($outTradeNo, $arr = array()){
    $query_foxpay = db_find_one('user_foxpay', array('orderid'=>$outTradeNo, 'pay_type'=>1, 'status'=>2));
    empty($query_foxpay) AND message(-1, '该订单不符合退款条件！');
    $query_user = db_find_one('user', array('uid'=>$query_foxpay['uid']), array(), array('rmbs'=>'rmbs'));
    if($query_foxpay['rmbs'] > $query_user['rmbs']){
        message(-1, '该用户余额不足，不符合退款条件！');
    }
    
    $appid = $arr['pay_appid'];
    empty($appid) AND message(-1, '支付宝开放平台APPID不能为空！');
    $tradeNo = $query_foxpay['trade_no'];
    $rsaPrivateKey = $arr['pay_privatekey'];
    empty($rsaPrivateKey) AND message(-1, '开放平台开发助手私钥不能为空！');
    $refundAmount = !empty($arr['pay_debug']) ? 0.01 : $query_foxpay['money'];
    empty($refundAmount) AND message(-1, '退款金额不能为空！');    
    $aliPay = new AlipayService();
    $aliPay->setAppid($appid);
    $aliPay->setRsaPrivateKey($rsaPrivateKey);
    $aliPay->setTradeNo($tradeNo);
    $aliPay->setOutTradeNo($outTradeNo);
    $aliPay->setRefundAmount($refundAmount);
    $result = $aliPay->doRefund();
    $result = $result['alipay_trade_refund_response'];
    if($result['code'] && $result['code'] == '10000'){
        fox_pay_add_foxlog($query_foxpay['uid'], $outTradeNo, $query_foxpay['rmbs'], 2, 0); //给积分插件传值
        db_update('user_foxpay', array('orderid'=>$outTradeNo), array('status'=>4));
        message(0, '订单已完成退款！');
    }else{
        message(-1, $result['sub_msg']);
    }
}
function fox_pay_add_foxlog($uid, $orderid, $rmbs = 0, $status = 0, $plus = 0){
    global $longip, $time;
    if(function_exists("fox_score_code_fmt")){
        if($uid && $orderid && $rmbs){
            $query_user = db_find_one('user', array('uid'=>$uid), array(), array('rmbs'=>'rmbs'));
            $old_num = $query_user['rmbs'];
            if($plus == 1){
                $new_num = $query_user['rmbs'] + $rmbs;
                $smg = '{exp3}+'.$rmbs;
            }else{
                $new_num = $query_user['rmbs'] - $rmbs;
                $smg = '已退款,{exp3}-'.$rmbs;
                db_update('user', array('uid'=>$uid), array('rmbs-'=>$rmbs));
            }
            $arr = array('uid'=>$uid, 'uip'=>$longip, 'old_num'=>$old_num, 'num'=>$new_num, 'msg_type'=>20, 'exp_type'=>3, 'time'=>$time, 'message'=>$smg, 'orderid'=>$orderid, 'state'=>$status);
            db_create('user_foxlog', $arr);
        }
    }
}

function fox_pay_get_username($uid){
    $q = user_read($uid);
    if(!empty($q)){
        return $q['username'];
    }
}
function fox_pay_total_money(){
    $query = db_find('user_foxpay', array('pay_type'=>1, 'status'=>2), array(), 1, 999999, '', array('money'));
    $r = arrlist_sum($query, 'money');
    return $r;
}
function fox_pay_log($s = '', $file = 'paylog'){
    if($s){
        global $conf, $time, $ip;
        $day = date('Ym', $time);
        $mtime = date('Y-m-d H:i:s', $time);
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $logpath = $conf['log_path'].$day;
        !is_dir($logpath) AND mkdir($logpath, 0777, true);
        $s = xn_json_encode($s);
        $s = "$mtime\t$ip\t$url\r\n$s\r\n";
        file_put_contents($logpath."/{$file}.php", $s, FILE_APPEND);
    }
}
?>