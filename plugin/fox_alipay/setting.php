<?php
/*

 */
!defined('DEBUG') AND exit('Access Denied.');
plugin_info($plugins['fox_alipay']);
$action = param(3, '');

if(empty($action)){
    if($method == 'GET'){
        $input = array();
        $input['pay_rmbstr'] = form_text('pay_rmbstr', !empty($fox_alipay_arr['pay_rmbstr']) ? $fox_alipay_arr['pay_rmbstr'] : '1|5|10|20|30|50|100|200|300|500');
        $input['pay_min'] = form_text('pay_min', !empty($fox_alipay_arr['pay_min']) ? $fox_alipay_arr['pay_min'] : 1);
        $input['pay_ratio'] = form_text('pay_ratio', !empty($fox_alipay_arr['pay_ratio']) ? $fox_alipay_arr['pay_ratio'] : 1);
        $input['pay_email'] = form_text('pay_email', !empty($fox_alipay_arr['pay_email']) ? $fox_alipay_arr['pay_email'] : '');
        $input['pay_appid'] = form_text('pay_appid', !empty($fox_alipay_arr['pay_appid']) ? $fox_alipay_arr['pay_appid'] : '');
        $input['pay_publickey'] = form_textarea('pay_publickey', !empty($fox_alipay_arr['pay_publickey']) ? $fox_alipay_arr['pay_publickey'] : '', '100%', 100);
        $input['pay_privatekey'] = form_textarea('pay_privatekey', !empty($fox_alipay_arr['pay_privatekey']) ? $fox_alipay_arr['pay_privatekey'] : '', '100%', 100);
        $input['pay_timeout'] = form_text('pay_timeout', !empty($fox_alipay_arr['pay_timeout']) ? $fox_alipay_arr['pay_timeout'] : '');
        $input['pay_debug'] = form_radio('pay_debug', array(0=>lang('no'), 1=>lang('yes')), !empty($fox_alipay_arr['pay_debug']) ? $fox_alipay_arr['pay_debug'] : 0);
        $header['title'] = '支付设置 · ' . $conf['sitename'];
        include _include(APP_PATH."plugin/fox_alipay/oddfox/template/fox_setting.php");
    }
    elseif($method == 'POST'){
        $act = param('act', '');
        if($act == 'setting'){
            $post = array();
            $post['pay_rmbstr'] = trim(param('pay_rmbstr'));
            $post['pay_min'] = trim(param('pay_min', 1));
            $post['pay_ratio'] = trim(param('pay_ratio', 1));
            $post['pay_email'] = trim(param('pay_email'));
            $post['pay_appid'] = trim(param('pay_appid'));
            $post['pay_publickey'] = trim(param('pay_publickey'));
            $post['pay_privatekey'] = trim(param('pay_privatekey'));
            $post['pay_timeout'] = trim(param('pay_timeout'));
            $post['pay_debug'] = param('pay_debug', 0);
            kv_cache_set('fox_alipay', $post);
            message(0, '保存设置成功！');
        }
    }
}
elseif($action == 'paylist'){
    if($method == 'GET'){
        $kwd = param('kwd', '');
        !empty($kwd) AND $kwd = xn_urldecode($kwd);
        $page = param(4, 1);
        $pagesize = $conf['pagesize'];
        if(empty($kwd)){
            $totalnum = db_count('user_foxpay', array('pay_type'=>1));
            $pagination = pagination(url("plugin-setting-fox_alipay-paylist-{page}"), $totalnum, $page, $pagesize);
            $paylist = db_find('user_foxpay', array('pay_type'=>1), array('id'=>-1), $page, $pagesize);
        } else {
            $totalnum = db_count('user_foxpay', array('uid'=>$kwd, 'pay_type'=>1));
            if($totalnum){
                $pagination = pagination(url("plugin-setting-fox_alipay-paylist-{page}")."?kwd=".xn_urlencode($kwd), $totalnum, $page, $pagesize);
                $paylist = db_find('user_foxpay', array('pay_type'=>1, 'uid'=>$kwd), array('id'=>-1),$page, $pagesize);
            }else{
                $totalnum = db_count('user_foxpay', array('orderid'=>$kwd, 'pay_type'=>1));
                if($totalnum){
                    $pagination = pagination(url("plugin-setting-fox_alipay-paylist-{page}")."?kwd=".xn_urlencode($kwd), $totalnum, $page, $pagesize);
                    $paylist = db_find('user_foxpay', array('pay_type'=>1, 'orderid'=>$kwd), array('id'=>-1),$page, $pagesize);
                }else{
                    $totalnum = db_count('user_foxpay', array('trade_no'=>$kwd, 'pay_type'=>1));
                    $pagination = pagination(url("plugin-setting-fox_alipay-paylist-{page}")."?kwd=".xn_urlencode($kwd), $totalnum, $page, $pagesize);
                    $paylist = db_find('user_foxpay', array('pay_type'=>1, 'trade_no'=>$kwd), array('id'=>-1),$page, $pagesize);
                }
            }
        }
        foreach ($paylist as &$val){
            $val['username'] = fox_pay_get_username($val['uid']);
            $val['pay_rmbs_fmt'] = sprintf("%.2f", $val['rmbs']);
            $val['create_date_fmt'] = date('Y-m-d H:i:s', $val['create_date']);
            $val['pay_date_fmt'] = !empty($val['pay_date']) ? date('H:i:s', $val['pay_date']) : '暂无';
            $val['trade_no_fmt'] = fox_pay_trade_no_fmt($val['trade_no'], $val['status']);
            $val['status_fmt'] = fox_pay_status_fmt($val['status']);
        }
        unset($val);
        $totalmoney = fox_pay_total_money();
        $header['title'] = '支付列表_' . $conf['sitename'];
        include _include(APP_PATH."plugin/fox_alipay/oddfox/template/fox_paylist.php");
    }
}
elseif($action == 'payclose'){
    if($method == 'POST'){
        $outTradeNo = param(4);        
        empty($outTradeNo) AND message(-1, '订单号不能为空！');
        fox_pay_trade_close($outTradeNo, $fox_alipay_arr);
    }
    message(-1, 'Access Denied.');
}
elseif($action == 'payrefund'){
    if($method == 'POST'){
        $outTradeNo = param(4);
        empty($outTradeNo) AND message(-1, '订单号不能为空！');
        fox_pay_trade_refund($outTradeNo, $fox_alipay_arr);
    }
    message(-1, 'Access Denied.');
}
else{
    message(-1, 'Access Denied.');
}
function plugin_info($fox_alipay){
    $error1 = '请先<a href="'.url("plugin-install-fox_alipay").'" class="text-danger">安装'.$fox_alipay['name'].'</a>，您已将该插件卸载。';
    $error2 = '请先<a href="'.url("plugin-enable-fox_alipay").'" class="text-danger">开启'.$fox_alipay['name'].'</a>，您已将该插件禁用。';
    empty($fox_alipay['installed']) AND message(-1, $error1);
    empty($fox_alipay['enable']) AND message(-1, $error2);
}
?>