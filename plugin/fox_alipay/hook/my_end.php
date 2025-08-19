
<?php exit;
elseif($action == 'pay'){
    if($method == 'GET'){
        $fox_pay_js_rmb_name = !empty($conf['rmb_name']) ? $conf['rmb_name'] : '修罗币';
        $fox_tips = '请考虑好再充值！一经充值不可退款且最低充值';
        $fox_tips .= '<span class="text-danger">'.$fox_alipay_arr['pay_min'].'</span>';
        $fox_tips .= !empty($conf['rmb_unit']) ? $conf['rmb_unit'] : '元';
        $fox_tips .= $fox_pay_js_rmb_name;
        $pay_rmbstr_arr[0] = '';
        $pay_rmbstr_arr = !empty($fox_alipay_arr['pay_rmbstr']) ? explode('|', $fox_alipay_arr['pay_rmbstr']) : array();
        $return_url = http_url_path() . url('my-paylist');
        $header['title'] = '帐户充值-'.$conf['sitename'];
        include _include(APP_PATH.'plugin/fox_alipay/oddfox/template/my_pay.php');
        
    }elseif($method == 'POST'){
        empty($user['uid']) AND message(-1, lang('user_not_exists'));
        $pay_money = param('amount');
        fox_pay_mypay_post($user, $pay_money, $fox_alipay_arr);
    }
}
elseif($action == 'paylist') {
        $page = param(2, 1);
        $pagesize = $conf['pagesize'];
        $listnum = db_count('user_foxpay',array('uid'=>$uid,'pay_type'=>1));
        $pagination = pagination(url("my-paylist-{page}"), $listnum, $page, $pagesize);
        $paylist = db_find('user_foxpay',array('uid'=>$uid, 'pay_type'=>1), array('id'=>-1), $page, $pagesize);
        foreach ($paylist as &$val){
            $val['pay_date_fmt'] = !empty($val['pay_date']) ? date('Y-m-d H:i:s', $val['pay_date']) : '暂无';
            $val['pay_trade_no_fmt'] = fox_pay_trade_no_fmt($val['trade_no'], $val['status']);
            $val['pay_rmbs_fmt'] = sprintf("%.2f", $val['rmbs']);
            $val['pay_status_fmt'] = fox_pay_status_fmt($val['status']);
        }
        unset($val);
        $header['title'] = '充值记录-'.$conf['sitename'];
        include _include(APP_PATH.'plugin/fox_alipay/oddfox/template/my_paylist.php');
}?>
